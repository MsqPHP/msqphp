<?php declare(strict_types = 1);
namespace msqphp\base\file;

use msqphp\base;
use msqphp\traits;

final class File
{
    const ERROR = [
        'CanNotOperable' => '无法操作',
        'NotExists'   => '不存在',
        'CanNotDelete'=>'无法删除',
        'CanNotWrite'=>'无法写入',
        'UnknownErro'=>'未知错误',
        'CanNotCopy'=>'无法复制',
        'CanNotAppend'=>'无法追加内容',
        'CanNotRead'=>'无法读取',
        'ParentDir'=>'父目录',
        'AlreadyExists'=>'已经存在',
    ];
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
                throw new FileException($file.static::ERROR['CanNotOperable'].','.static::ERROR['CanNotDelete']);
            }

            if (!unlink($file)) {
                throw new FileException($file.static::ERROR['UnknownErro'].','.static::ERROR['CanNotDelete']);
            }
        } else{
            if (!$force) {
                throw new FileException($file.static::ERROR['NotExists'].','.static::ERROR['CanNotDelete']);
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
            throw new FileException($file.static::ERROR['NotExists'].','.static::ERROR['CanNotRead']);
        }
        if (!is_readable($file)) {
            throw new FileException($file.static::ERROR['CanNotOperable'].','.static::ERROR['CanNotRead']);
        }

        //读取内容
        $fp = fopen($file, 'r');
        $content = (string) fread($fp, $len);
        fclose($fp);
        unset($fp);

        //无法读取
        if(false === $content) {
            throw new FileException($file.static::ERROR['UnknownErro'].','.static::ERROR['CanNotRead']);
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
            throw new FileException($file.static::ERROR['NotExists'].','.static::ERROR['CanNotRead']);
        }
        if (!is_readable($file)) {
            throw new FileException($file.static::ERROR['CanNotOperable'].','.static::ERROR['CanNotRead']);
        }

        $content = file_get_contents($file);

        //无法读取
        if(false === $content) {
            throw new FileException($file.static::ERROR['UnknownErro'].','.static::ERROR['CanNotRead']);
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
                throw new FileException($file.static::ERROR['NotExists'].','.static::ERROR['CanNotAppend']);
            }
        } else {
        //文件存在
            if (!is_writable($file)) {
                throw new FileException($file.static::ERROR['CanNotOperable'].','.static::ERROR['CanNotAppend']);
            }

            if (false === file_put_contents($file, (string)$content, FILE_APPEND)) {
                throw new FileException($file.static::ERROR['UnknownErro'].','.static::ERROR['CanNotAppend']);
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
    public static function write(string $file, $content, bool $force = false, int $code = 0640)
    {
        //父目录
        $parent_dir = dirname($file);

        //目录不存在
        if (!is_dir($parent_dir)) {
            //错
            if (!$force) {
                throw new FileException($file.static::ERROR['ParentDir'].static::ERROR['NotExists'].','.static::ERROR['CanNotWrite']);
            } else {
                //创建
                base\dir\Dir::make($parent_dir, true);
            }
        }

        if (!is_writable($parent_dir)) {
            throw new FileException($file.static::ERROR['ParentDir'].','.static::ERROR['CanNotWrite']);
        }

        if (false === file_put_contents($file, (string)$content) || chmod($file, $code)) {
            throw new FileException($file.static::ERROR['UnknownErro'].','.static::ERROR['CanNotWrite']);
        }
    }
    //写入文件别名
    public static function save(string $file, $content, bool $force = false, int $code = 0640)
    {
        static::write($file, $content, $force, $code);
    }

    /**
     * 复制文件
     * @param  string $from       文件路径
     * @param  string $to         文件路径
     * @param  string $force      不存在则创建, 存在则替换
     * @throws FileException
     * @return void
     */
    public static function copy(string $from, string $to, bool $force = false)
    {
        //是否存在
        if (!is_file($from)) {
            throw new FileException($from.static::ERROR['NotExists'].','.static::ERROR['CanNotCopy']);
        }

        // //是否可操作
        // if (!is_executable($from) || !is_writable($from)) {
        //     throw new FileException($from.static::ERROR['CanNotOperable'].','.static::ERROR['CanNotCopy']);
        // }

        //对应文件是否存在
        if (is_file($to)) {
            if ($force) {
                static::delete($to, true);
            } else {
                throw new FileException($to.static::ERROR['AlreadyExists'].','.static::ERROR['CanNotCopy']);
            }
        }

        //对应文件父目录是否可操作
        $to_dir = dirname($to);

        // if (!is_writable($to_dir) || !is_executable($to_dir)) {
        //     throw new FileException($to.static::ERROR['ParentDir'].static::ERROR['CanNotOperable'].','.static::ERROR['CanNotCopy']);
        // }

        //复制
        if (!copy($from, $to)) {
            throw new FileException($from.static::ERROR['UnknownErro'].','.static::ERROR['CanNotCopy']);
        }
    }
}