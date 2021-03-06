<?php

namespace Cool\Foundation\Application;

use  Cool\Contracts\Foundation\ComponentInterface;

/**
 * Trait ComponentInitializeTrait
 * @package Cool\Foundation\Application
 */
trait ComponentInitializeTrait
{

    /**
     * 获取组件
     * @param $name
     * @return ComponentInterface
     */
    public function get($name)
    {
        $component = $this->container->get($name);
        // 触发前置处理事件
        self::triggerBeforeInitialize($component);
        // 修改引用组件状态
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE && $component->getStatus() == ComponentInterface::STATUS_READY) {
            $component->setStatus(ComponentInterface::STATUS_RUNNING);
        }
        // 停用未初始化的组件
        if ($component->getStatus() != ComponentInterface::STATUS_RUNNING) {
            return new ComponentDisabled($component, $name);
        }
        // 返回组件
        return $component;
    }

    /**
     * 获取组件
     * 为了兼容旧版本，保留这个方法
     * 由于PHP 的 __get 魔术方法，不让并行调用同名属性，导致无法包含协程切换 https://github.com/swoole/swoole-src/issues/2625
     * @param $name
     * @return \Mix\Core\Component\ComponentInterface
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * 清扫组件容器
     */
    public function cleanComponents()
    {
        // 触发后置处理事件
        foreach (array_keys($this->components) as $name) {
            if (!$this->container->has($name)) {
                continue;
            }
            $component = $this->container->get($name);
            self::triggerAfterInitialize($component);
        }
    }

    /**
     * 触发前置处理事件
     * @param $component
     */
    protected static function triggerBeforeInitialize($component)
    {
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE) {
            return;
        }
        if ($component->getStatus() == ComponentInterface::STATUS_READY) {
            $component->onBeforeInitialize();
        }
    }

    /**
     * 触发后置处理事件
     * @param $component
     */
    protected static function triggerAfterInitialize($component)
    {
        if (self::getCoroutineMode($component) == ComponentInterface::COROUTINE_MODE_REFERENCE) {
            return;
        }
        if ($component->getStatus() == ComponentInterface::STATUS_RUNNING) {
            $component->onAfterInitialize();
        }
    }

    /**
     * 获取协程模式
     * @param $component
     * @return bool
     */
    protected static function getCoroutineMode($component)
    {
        $class = get_class($component);
        return $class::COROUTINE_MODE ?? false;
    }

}
