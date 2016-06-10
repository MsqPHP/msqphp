<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;
use msqphp\traits;

final class Dir
{
    use traits\CallStatic;
    /**
     * 创建目录
     * @param  string $dir    路径
     * @param  bool   $force  是否强制创建父目录 或 忽略目录是否已经创建
     * @param  int    $code   读写执行代码
     * @throws DirException
     * @return void
     */
    public static function make(string $dir, bool $force = false, int $code = 0755)
    {
        //是否目录已存在
        if (is_dir($dir)) {
            //目录已存在
            if (!$force) {
                throw new DirException($dir.' 目录已存在');
            }
        } else {
            //判断父目录是否存在
            $parent_dir = dirname($dir);

            if (!is_dir($parent_dir)) {
                if ($force) {
                    //创建
                    Dir::make($parent_dir, true, $code);
                } else {
                    //父目录不存在
                    throw new DirException($dir.' 父目录不存在');
                }
            }

            //父目录是否可写
            if (!is_writable($parent_dir)) {
                throw new DirException($dir.'上级目录不可操作, 无法创建');
            }

            //创建目录
            if (!mkdir($dir, $code) || !chmod($dir, $code)) {
                throw new DirException($dir.'未知错误, 无法创建');
            }

        }
    }
    /**
     * 删除目录
     * @func_name     deleteDir
     * @param  string $dir 路径
     * @param  bool   $force    忽略是否存在, 强制删除
     * @throws DirException
     * @return void
     */
    public static function delete(string $dir, bool $force = false)
    {
        //目录是否存在
        if(is_dir($dir)) {
            if (!$force) {
                throw new DirException($dir.'目录不存在, 无法删除');
            }
        } else {
            //是否可操作
            if (!is_writable($dir)) {
                throw new DirException($dir.'目录不可操作, 无法删除');
            }
            //如果强制，先清空目录
            $force === true && Dir::empty($dir);
            //检测是否为空
            if (!Dir::isEmpty($dir)) {
                throw new DirException($dir.' 目录不为空, 无法删除');
            }
            //删除目录
            if (!rmdir($dir)) {
                throw new DirException($dir.'未知错误, 无法删除');
            }
        }
    }
    public static function drop(string $dir, bool $force = false)
    {
        Dir::drop($dir, $force);
    }
}