<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/20
 * Time: 15:14
 */

namespace Cool\Support;

/**
 * Class JsonFormat
 * @package Cool\Support
 */
class JsonFormat
{
// 编码
    public static function encode($value, $options = 0, $depth = 512)
    {
        return json_encode($value, $options, $depth);
    }

    // 解码
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($json, $assoc, $depth, $options);
    }
}