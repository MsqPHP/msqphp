<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

/**
 * 通过文件名称删除文件
 * @func_name     deleteAllFileByType
 * @param  string $path   目录路径
 * @param  string $type   后缀名
 * @param  string $pre    前缀名
 * @throws DirException
 * @return void
 */
return function (string $dir, string $type, string $pre = '')
{
    foreach (Dir::getAllFileByType($dir, $type, $pre) as $file) {
        base\file\File::delete($file, true);
    }
};