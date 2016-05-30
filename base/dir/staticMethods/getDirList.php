<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;
use msqphp\traits;

/**
 * 得到当前目录列表
 * @func_name       getDirList
 * @param  string   $dir  路径
 * @param  bool     $type 是否返回绝对路径
 * @throws DirException
 * @return array
 */
return function (string $dir, bool $absolute = false) : array
{

    if (!is_dir($dir)) {
        throw new DirException($dir.'文件夹不存在');
    }
    if (!is_readable($dir)) {
        //无法操作
        throw new DirException($dir.'文件夹不可读');
    }

    $dir = realpath($dir) . DIRECTORY_SEPARATOR;

    $list = scandir($dir, 0);

    if ($absolute) {
        $result = [];

        foreach ($list as $path) {
            $path !== '.' && $path !== '..' && is_dir($dir.$path) && $result[]= $dir.$path;
        }

        return $result;
    } else {
        return array_filter($list, function($path) use ($dir) {
            return $path !== '.' && $path !== '..' && is_dir($dir.$path);
        }, 0);
    }
};