<?php declare(strict_types = 1);
namespace msqphp\base\file;
/**
 * 复制文件
 * @func_name                 copy 
 * @param  string $from       文件路径
 * @param  string $to         文件路径
 * @param  string $force      不存在则创建, 存在则替换
 * @throws FileException
 * @return void
 */
return function (string $from, string $to, bool $force = false)
{
    //是否存在
    if (!is_file($from)) {
        throw new FileException($from.'不存在, 无法复制');
    }

    //是否可操作
    if (!is_executable($from) || !is_writable($from)) {
        throw new FileException($from.'无法操作, 无法复制');
    }

    //对应文件是否存在
    if (is_file($to)) {
        if ($force) {
            static::deleteFile($to, true);
        } else {
            throw new FileException($to.'已存在, 无法复制');
        }
    }

    //对应文件父目录是否可操作
    $to_dir = dirname($to);

    if (!is_writable($to_dir) || !is_executable($to_dir)) {
        throw new FileException($to.'父目录无法操作, 无法复制');
    }

    //复制
    if (!copy($from, $to)) {
        throw new FileException('未知错误.无法复制');
    }
};