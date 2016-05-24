<?php declare(strict_types = 1);
namespace msqphp\base\file;

use msqphp\base;
use msqphp\traits;

class File
{
    use traits\CallStatic;
    /**
     * 删除文件
     * @param  string       $file  文件路径
     * @param  bool|boolean $force 是否强制(忽略不存在)
     * @throws FileException
     * @return vodi
     */
    public static function drop(string $file, bool $force = false)
    {
        static::delete($file, $force);
    }
    public static function delete(string $file, bool $force = false)
    {
        if (is_file($file)) {
            if (!is_writable($file)) {
                throw new FileException($file.'文件不可操作, 无法删除');
            }

            if (!unlink($file)) {
                throw new FileException($file.'未知错误, 无法删除');
            }
        } else{
            if (!$force) {
                throw new FileException($file.'文件不存在, 无法删除');
            }
        }
    }
    /**
     * 读取指定长度的文件内容
     * @param  string $file 目标文件路径
     * @param  string $len  长度
     * @throws FileException
     * @return string
     */
    public static function read(string $file, int $len) : string
    {
        if (!is_file($file)) {
            throw new FileException($file.'不存在');
        }
        if (!is_readable($file)) {
            throw new FileException($file.'不可读');
        }

        //读取内容
        $fp = fopen($file, 'r');
        $content = (string) fread($fp, $len);
        fclose($fp);
        unset($fp);

        //无法读取, 或者读取内容小于指定长度
        if(false === $content || strlen($content) < $len) {
            throw new FileException($file.'读取'.$len.'长度，但文件无法读取或者长度不够');
        }

        return $content;
    }
    /**
     * 获得文件内容
     * @param  string $file 目标文件路径
     * @throws FileException
     * @return string
     */
    public static function get(string $file) : string
    {
        if (!is_file($file)) {
            throw new FileException($file.'不存在');
        }
        if (!is_readable($file)) {
            throw new FileException($file.'不可读');
        }

        $content = file_get_contents($file);

        if (false === $content) {
            throw new FileException($file.'未知错误, 无法读取');
        }

        return $content;
    }
    /**
     * 追加文件内容
     * @param  string     $file     目标文件路径
     * @param  string|int $content  追加内容
     * @param  string     $force    当文件不存在的时候是否强制创建
     * @throws FileException
     * @return void
     */
    public static function append(string $file, $content, bool $force = false)
    {
        //文件不存在
        if (!is_file($file)) {
            if ($force) {
                static::write($file, $content, true);
            } else {
                throw new FileException($file.' 文件不存在');
            }
        } else {
        //文件存在
            if (!is_writable($file)) {
                throw new FileException($file.' 文件不可写');
            }

            if (false === file_put_contents($file, (string)$content, FILE_APPEND)) {
                throw new FileException($file.' 无法追加内容');
            }
        }
    }
    /**
     * 重写文件|保存文件
     * @param  string     $file     路径
     * @param  string|int $content  写入内容
     * @param  bool       $force    如果父文件夹不存在，强制创建
     * @throws FileException
     * @return void
     */
    public static function write(string $file, $content, bool $force = false)
    {
        //父目录
        $parent_dir = dirname($file);

        //目录不存在
        if (!is_dir($parent_dir)) {
            //错
            if (!$force) {
                throw new FileException($file.'父目录不存在');
            } else {
                //创建
                base\dir\Dir::make($parent_dir, true);
            }
        }

        if (!is_writable($parent_dir)) {
            throw new FileException($file.'父目录不可写');
        }

        if (false === file_put_contents($file, (string)$content)) {
            throw new FileException($file.'未知错误, 无法写入');
        }
    }
    //写入文件别名
    public static function save(string $file, $content, bool $force = false)
    {
        static::write($file, $content, $force);
    }
}