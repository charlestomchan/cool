<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/23
 * Time: 19:00
 */

namespace Cool\Validate;


class UrlValidator extends BaseValidator
{
// 初始化选项
    protected $_initOptions = ['url'];

    // 启用的选项
    protected $_enabledOptions = ['length', 'minLength', 'maxLength'];

    // 类型验证
    protected function url()
    {
        $value = $this->attributeValue;
        if (!Validate::isUrl($value)) {
            // 设置错误消息
            $defaultMessage = "{$this->attribute}不符合网址格式.";
            $this->setError(__FUNCTION__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }
}