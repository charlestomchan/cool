<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/23
 * Time: 18:58
 */

namespace Cool\Validate;


class EmailValidator extends BaseValidator
{

    // 初始化选项
    protected $_initOptions = ['email'];

    // 启用的选项
    protected $_enabledOptions = ['length', 'minLength', 'maxLength'];

    // 类型验证
    protected function email()
    {
        $value = $this->attributeValue;
        if (!Validate::isEmail($value)) {
            // 设置错误消息
            $defaultMessage = "{$this->attribute}不符合邮箱格式.";
            $this->setError(__FUNCTION__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }
}