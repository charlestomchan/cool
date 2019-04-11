<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/23
 * Time: 18:59
 */

namespace Cool\Validate;


class StringValidator extends BaseValidator
{
// 启用的选项
    protected $_enabledOptions = ['length', 'minLength', 'maxLength', 'filter'];

    // 过滤处理
    protected function filter($param)
    {
        foreach ($param as $value) {
            switch ($value) {
                case 'trim':
                    $this->attributeValue = trim($this->attributeValue);
                    break;
                case 'strip_tags':
                    $this->attributeValue = strip_tags($this->attributeValue);
                    break;
                case 'htmlspecialchars':
                    $this->attributeValue = htmlspecialchars($this->attributeValue);
                    break;
            }
        }
        return true;
    }

}