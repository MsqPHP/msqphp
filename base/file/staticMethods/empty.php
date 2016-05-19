<?php declare(strict_types = 1);
namespace msqphp\base\file;
/**
 * 清空文件内容
 * @func_name     empty
 * @param  string $path     文件路径
 * @param  bool   $force    为空创建
 * @throws FileException
 * @return bool
 */
return function (string $path, bool $force = false) : bool
{
    //文件不存在
    if (!is_file($path)) {
        if ($force) {
            return static::write($path, '', true);
        } else {
            throw new FileException($path.'文件不存在, 无法清空');
        }
    } else {
        //创建一个空文件
        return static::write($path, '', true);
    }
};