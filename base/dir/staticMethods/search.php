<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;
use msqphp\traits;

/**
 * 根据搜索内容查找所有文件
 * @func_name     search
 * @param  string $dir    目录
 * @param  string $search 搜索内容
 * @param  int    $max    个数
 * @throws DirException
 * @return miexd
 */
return function (string $dir, string $search, int $max = 0)
{

    if (!is_dir($dir)) {
        throw new DirException($dir.' 文件夹不存在');
    }

    if (!is_writable($dir)) {
        throw new DirException($dir.' 文件夹无法读取');
    }

    $result = array_merge(static::getAllDir($dir), static::getAllFile($dir));

    $serached = [];

    $max === 0 && $max = PHP_INT_MAX;

    for ($i = 0; $i < $max; ++ $i) {
        fasle !== strpos($search , $result[$i]) && $serached[] = $result[$i];
    }

    return $max === 1 ? $serached[0] : $serached;
};