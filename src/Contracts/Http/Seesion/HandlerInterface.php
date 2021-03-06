<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/23
 * Time: 16:54
 */

namespace Cool\Contracts\Http\Seesion;

/**
 * Interface HandlerInterface
 * @package Cool\Contracts\Http\Seesion
 */
interface HandlerInterface
{

    /**
     * 加载 SessionId
     * @param $name
     * @param $maxLifetime
     * @return bool
     */
    public function loadSessionId($name, $maxLifetime);

    /**
     * 创建 SessionId
     * @param $sessionIdLength
     * @param $maxLifetime
     * @return bool
     */
    public function createSessionId($sessionIdLength, $maxLifetime);

    /**
     * 获取 SessionId
     * @return string
     */
    public function getSessionId();

    /**
     * 赋值
     * @param $key
     * @param $value
     * @param $name
     * @param $maxLifetime
     * @param $cookieExpires
     * @param $cookiePath
     * @param $cookieDomain
     * @param $cookieSecure
     * @param $cookieHttpOnly
     * @return bool
     */
    public function set($key, $value, $name, $maxLifetime, $cookieExpires, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);

    /**
     * 取值
     * @param null $key
     * @return mixed
     */
    public function get($key = null);


    /**
     * 删除
     * @param $key
     * @return bool
     */
    public function delete($key);

    /**
     * 清除session
     * @return bool
     */
    public function clear();

    /**
     * 判断是否存在
     * @param $key
     * @return bool
     */
    public function has($key);

}