<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/22
 * Time: 16:34
 */

namespace Cool\Foundation\Bean;


use Cool\Foundation\Bean;
use Cool\Foundation\Exceptions\ConfigException;

trait ObjectTrait
{
    /**
     * 构造
     * BeanObject constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        // 执行构造事件
        $this->onConstruct();
        // 构建配置
        $config = \Cool::configure($config);
        // 导入属性
        \Cool::importProperties($this, $config);
        // 执行初始化事件
        $this->onInitialize();
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        $this->onDestruct();
    }

    /**
     * 构造事件
     */
    public function onConstruct()
    {
    }

    /**
     * 初始化事件
     */
    public function onInitialize()
    {
    }

    /**
     * 析构事件
     */
    public function onDestruct()
    {
    }

    /**
     * 使用依赖创建实例
     * @param $name
     * @return $this
     */
    public static function newInstance($name = null)
    {
        $currentClass = get_called_class();
        $bean         = Bean::config(is_null($name) ? Bean::name($currentClass) : $name);
        $class        = $bean['class'];
        $properties   = $bean['properties'] ?? [];
        if ($class != $currentClass) {
            throw new ConfigException("Bean class is not equal to the current class, Current class: {$currentClass}, Bean class: {$class}");
        }
        return new $class($properties);
    }
}