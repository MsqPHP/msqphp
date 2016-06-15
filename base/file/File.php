<?php declare(strict_types = 1);
namespace msqphp\base\file;

use msqphp\base;
use msqphp\traits;

final class File
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

            $parent_dir = dirname($file);

            if (!is_writable($parent_dir)) {
                throw new FileException($file.'父目录,无法写入');
            }
            if (!is_executable($parent_dir)) {
                throw new FileException($file.'父目录,无法执行');
            }

            if (!unlink($file)) {
                throw new FileException($file.'未知错误,无法删除');
            }
        } else{
            if (!$force) {
                throw new FileException($file.'不存在,无法删除');
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
            throw new FileException($file.'不存在,无法读取');
        }
        if (!is_readable($file)) {
            throw new FileException($file.'无法操作,无法读取');
        }

        //读取内容
        $fp = fopen($file, 'r');
        $content = (string) fread($fp, $len);
        fclose($fp);
        unset($fp);

        //无法读取
        if(false === $content) {
            throw new FileException($file.'未知错误,无法读取');
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
            throw new FileException($file.'不存在,无法读取');
        }
        if (!is_readable($file)) {
            throw new FileException($file.'无法操作,无法读取');
        }

        $content = file_get_contents($file);

        //无法读取
        if(false === $content) {
            throw new FileException($file.'未知错误,无法读取');
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
                throw new FileException($file.'不存在,无法追加内容');
            }
        } else {
        //文件存在
            if (!is_writable($file)) {
                throw new FileException($file.'父目录,无法写入');
            }

            if (false === file_put_contents($file, (string)$content, FILE_APPEND)) {
                throw new FileException($file.'未知错误,无法追加内容');
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
    public static function write(string $file, $content, bool $force = false, int $code = 0644)
    {
        //父目录
        $parent_dir = dirname($file);

        //目录不存在
        if (!is_dir($parent_dir)) {
            //错
            if (!$force) {
                throw new FileException($file.'父目录不存在,无法写入');
            } else {
                //创建
                base\dir\Dir::make($parent_dir, true);
            }
        }

        if (!is_writable($parent_dir)) {
            throw new FileException($file.'父目录,无法写入');
        }
        if (!is_executable($parent_dir)) {
            throw new FileException($file.'父目录,无法执行');
        }
        if (false === file_put_contents($file, (string)$content, LOCK_EX) || false === chmod($file, $code)) {
            throw new FileException($file.'未知错误,无法写入');
        }
    }
    //写入文件别名
    public static function save(string $file, $content, bool $force = false, int $code = 0640)
    {
        static::write($file, $content, $force, $code);
    }
}