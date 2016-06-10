<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;


/**
 * 目录是否为空
 * @param  string $dir      目录路径
 * @throws DirException
 * @return bool
 */
return function(string $dir) : bool
{
    if (!is_dir($dir)) {
        throw new DirException($dir.' 不存在');
    }
    if (!is_readable($dir)) {
        throw new DirException($dir.'不可读');
    }
    //scandir 获得当前目录列表, 如果为空, 则只有 . 和 ..
    return count(scandir($dir)) === 2;
};