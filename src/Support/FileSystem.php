<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/3/19
 * Time: 19:08
 */

namespace Cool\Support;

/**
 * Class FileSystem
 * @package Cool\Support
 */
class FileSystem
{
    /**
     * 返回路径中的目录部分，正反斜杠linux兼容处理
     * @param $path
     * @return string
     */
    public static function dirname($path)
    {
        if (strpos($path, '\\') === false) {
            return dirname($path);
        }
        return str_replace('/', '\\', dirname(str_replace('\\', '/', $path)));
    }

    /**
     * 返回路径中的文件名部分，正反斜杠linux兼容处理
     * @param $path
     * @return string
     */
    public static function basename($path)
    {
        if (strpos($path, '\\') === false) {
            return basename($path);
        }
        return str_replace('/', '\\', basename(str_replace('\\', '/', $path)));
    }

    /**
     * 判断是否为绝对路径
     * @param $path
     * @return bool
     */
    public static function isAbsolute($path)
    {
        if (($position = strpos($path, './')) !== false && $position <= 2) {
            return false;
        }
        if (strpos($path, ':') !== false) {
            return true;
        }
        if (substr($path, 0, 1) === '/') {
            return true;
        }
        return false;
    }

    /**
     * 删除指定的文件夹及其内容
     * @param $dir
     * @return bool
     */
    public static function deleteFolder($dir)
    {
        $dh = @opendir($dir);
        if (!$dh) {
            return false;
        }
        while (false !== ($file = readdir($dh))) {
            if (($file != '.') && ($file != '..')) {
                $full = $dir . '/' . $file;
                if (is_dir($full)) {
                    self::deleteFolder($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dh);
        rmdir($dir);
        return true;
    }

}