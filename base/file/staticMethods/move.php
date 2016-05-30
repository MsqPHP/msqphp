<?php declare(strict_types = 1);
namespace msqphp\base\file;
/**
 * 移动目录, 文件
 * @func_name          move
 * @param  string $from       目录路径
 * @param  string $to         目标路径
 * @throws  FileException
 * @return void
 */
return function (string $from, string $to)
{
    static::copy($from, $to);
    static::delete($from);
};