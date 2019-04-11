<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/19
 * Time: 16:39
 */

namespace Cool\Foundation;


use Cool\Foundation\Bean\AbstractObject;
use Cool\Foundation\Exceptions\ConfigException;

/**
 * Class Environment
 * @package Cool\Foundation
 */
class Environment extends AbstractObject
{

    /**
     * 文件名
     * @var string
     */
    public $filename;

    /**
     * 数据
     * @var array
     */
    protected $_data = [];

    /**
     * 加载环境配置
     * @return bool
     */
    public function load()
    {
        if (!is_file($this->filename)) {
            throw new ConfigException("Environment file does not exist: {$this->filename}");
        }
        $this->_data = array_merge(parse_ini_file($this->filename), $_SERVER, $_ENV);
        return true;
    }

    /**
     * 获取配置
     * @param $name
     * @param string $default
     * @return mixed
     */
    public function section($name, $default = '')
    {
        if (!isset($this->_data[$name])) {
            return $default;
        }
        return $this->_data[$name];
    }

    /**
     * 返回全部数据
     * @return array
     */
    public function sections()
    {
        return $this->_data;
    }
}