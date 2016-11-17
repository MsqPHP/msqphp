<?php declare(strict_types = 1);
namespace msqphp\base\dir;

/**
 * 移动目录, 文件
 *
 * @func_name                 move
 *
 * @param  string $from       目录路径
 * @param  string $to         目标路径
 *
 * @throws DirException
 * @return void
 */
return function (string $from, string $to) : void {
    static::copy($from, $to);
    static::delete($from);
};