<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/23
 * Time: 19:00
 */

namespace Cool\Validate;


use Cool\Foundation\Bean\AbstractObject;
use Cool\Foundation\Exceptions\InvalidArgumentException;

class Validator extends AbstractObject
{
    // 全部属性
    public $attributes;

    // 当前场景
    protected $_scenario;

    // 验证器类路径
    protected $_validators = [
        'integer'      => \Cool\Validate\IntegerValidator::class,
        'double'       => \Cool\Validate\DoubleValidator::class,
        'alpha'        => \Cool\Validate\AlphaValidator::class,
        'alphaNumeric' => \Cool\Validate\AlphaNumericValidator::class,
        'string'       => \Cool\Validate\StringValidator::class,
        'in'           => \Cool\Validate\InValidator::class,
        'date'         => \Cool\Validate\DateValidator::class,
        'email'        => \Cool\Validate\EmailValidator::class,
        'phone'        => \Cool\Validate\PhoneValidator::class,
        'url'          => \Cool\Validate\UrlValidator::class,
        'compare'      => \Cool\Validate\CompareValidator::class,
        'match'        => \Cool\Validate\MatchValidator::class,
        'call'         => \Cool\Validate\CallValidator::class,
        'file'         => \Cool\Validate\FileValidator::class,
        'image'        => \Cool\Validate\ImageValidator::class,
    ];

    // 错误
    protected $_errors = [];

    // 规则
    public function rules()
    {
        return [];
    }

    // 场景
    public function scenarios()
    {
        return [];
    }

    // 消息
    public function messages()
    {
        return [];
    }

    // 设置当前场景
    public function setScenario($scenario)
    {
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidArgumentException("场景不存在：{$scenario}");
        }
        if (!isset($scenarios[$scenario]['required'])) {
            throw new InvalidArgumentException("场景 {$scenario} 未定义 required 选项");
        }
        if (!isset($scenarios[$scenario]['optional'])) {
            $scenarios[$scenario]['optional'] = [];
        }
        $this->_scenario = $scenarios[$scenario];
    }

    // 验证
    public function validate()
    {
        if (!isset($this->_scenario)) {
            throw new InvalidArgumentException("场景未设置");
        }
        $this->_errors      = [];
        $scenario           = $this->_scenario;
        $scenarioAttributes = array_merge($scenario['required'], $scenario['optional']);
        $rules              = $this->rules();
        $messages           = $this->messages();
        // 判断是否定义了规则
        foreach ($scenarioAttributes as $attribute) {
            if (!isset($rules[$attribute])) {
                throw new InvalidArgumentException("属性 {$attribute} 未定义规则");
            }
        }
        // 验证器验证
        foreach ($rules as $attribute => $rule) {
            if (!in_array($attribute, $scenarioAttributes)) {
                continue;
            }
            $validatorType = array_shift($rule);
            if (!isset($this->_validators[$validatorType])) {
                throw new InvalidArgumentException("属性 {$attribute} 的验证类型 {$validatorType} 不存在");
            }
            $attributeValue = isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : null;
            // 实例化
            $validatorClass           = $this->_validators[$validatorType];
            $validator                = new $validatorClass([
                'isRequired'     => in_array($attribute, $scenario['required']),
                'options'        => $rule,
                'attribute'      => $attribute,
                'attributeValue' => $attributeValue,
                'messages'       => $messages,
                'attributes'     => $this->attributes,
            ]);
            $validator->mainValidator = $this;
            // 验证
            if (!$validator->validate()) {
                // 记录错误消息
                $this->_errors[$attribute] = $validator->errors;
            }
        }
        return empty($this->_errors);
    }

    // 返回全部错误
    public function getErrors()
    {
        return $this->_errors;
    }

    // 返回一条错误
    public function getError()
    {
        $errors = $this->_errors;
        if (empty($errors)) {
            return '';
        }
        $item  = array_shift($errors);
        $error = array_shift($item);
        return $error;
    }

}