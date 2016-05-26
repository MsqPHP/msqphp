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
            if (!$force) {
                //目录已存在
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
            if (!mkdir($dir, $code)) {
                throw new DirException($dir.'未知错误, 无法创建');
            }
        }
    }
    /**
     * 复制目录
     * @param  string $from       目录路径
     * @param  string $to         目标路径
     * @param  string $force      不存在则创建, 存在则替换
     * @throws DirException
     * @return void
     */
    public static function copy(string $from, string $to, bool $force = false)
    {
        //原目录是否存在
        if (!is_dir($from)) {
            throw new DirException($from.' 目录不存在');
        }

        //是否可操作
        if (!is_writable($from) || !is_executable($from)) {
            throw new DirException($from.' 无法操作');
        }

        //目标目录是否存在
        if (is_dir($to)) {
            if ($force) {
                Dir::empty($to, true);
            } else {
                throw new DirException($to.' 目录已存在');
            }
        } else {
            Dir::make($to);
        }
        $to_parent = dirname($to);
        //目标父目录是否可操作
        if (!is_writable($to_parent) || !is_executable($to_parent)) {
            throw new DirException($to.' 父目录无法操作');
        }

        $from = realpath($from) . DIRECTORY_SEPARATOR;
        $to   = realpath($to)   . DIRECTORY_SEPARATOR;

        //复制目录
        foreach (Dir::getDirList($from, false) as $dir) {
            Dir::copy($from.$dir, $to.$dir, true);
        }
        //复制文件
        foreach (Dir::getFileList($from, false) as $file) {
            base\file\File::copy($from.$file, $to.$file, true);
        }
    }

    /**
     * 目录是否为空
     * @param  string $dir      目录路径
     * @throws DirException
     * @return bool
     */
    public static function isEmpty(string $dir) : bool
    {
        if (!is_dir($dir)) {
            throw new DirException($dir.' 不存在');
        }
        if (!is_readable($dir)) {
            throw new DirException($dir.'不可读');
        }
        //scandir 获得当前目录列表, 如果为空, 则只有 . 和 ..
        return count(scandir($dir)) === 2;
    }

    /**
     * 清空目录内容
     * @func_name               empty
     * @param  string $dir      目录路径
     * @param  bool   $force    为空创建
     * @throws DirException
     * @return void
     */
    public static function empty(string $dir, bool $force = false)
    {
        //目录检测
        if (!is_dir($dir)) {
            if ($force) {
                Dir::make($dir, $force);
            } else {
                throw new DirException($dir.'目录不存在, 无法清空');
            }
        } else {
            //权限判断
            if (!is_writable($dir)) {
                throw new DirException($dir.'目录不可操作, 无法清空');
            }
            //清空目录
            foreach (Dir::getDirList($dir, true) as $children_dir) {
                Dir::delete($children_dir, true);
            }
            foreach (Dir::getFileList($dir, true) as $children_file) {
                base\file\File::delete($children_file, true);
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
    /**
     * 得到目录文件大小
     * @func_name     getDirSize
     * @param   string  $path 路径
     * @param   bool    $round      是否保留整数
     * @param   bool    $unit       是否带单位
     * @throws  DirException
     * @rely on msqphp\base\number\Number::byte();
     * @return  strging|int
     */
    public static function getSize(string $dir, bool $round = true, bool $unit = true)
    {

        if (!is_dir($dir)) {
            throw new DirException($dir.' 文件夹不存在');

        }
        if (!is_readable($dir)) {
            throw new DirException($dir.' 文件夹不可读');
        }

        $size = 0;

        foreach (Dir::getDirList($dir, true) as $children_dir) {
            $size += Dir::getSize($children_dir, false, false);
        }
        foreach (Dir::getFileList($dir, true) as $children_file) {
            $size += base\file\File::getSize($children_file, false, false);
        }

        $round && $size = round($size);

        $unit  && $size = base\number\Number::byte($size);

        return $size;
    }
    /**
     * 获得一个目录映射
     * @param  string      $dir  目录
     * @param  int|integer $deep 深度
     * @return array
     */
    public static function map(string $dir, int $deep = 100000000) : array
    {
        if ($deep === 0) {
            return [];
        }
        if (!is_dir($dir)) {
            throw new DirException($dir.'不存在');
        }
        if (!is_readable($dir)) {
            throw new DirException($dir.'不可读');
        }
        $map = [];
        $dir = realpath($dir) . DIRECTORY_SEPARATOR;
        foreach (static::getDirList($dir, false) as $children_dir) {
            $map[$children_dir] = static::map($dir.$children_dir, $deep - 1);
        }
        foreach (static::getFileList($dir, false) as $children_file) {
            $map[] = $children_file;
        }
        return $map;
    }
}