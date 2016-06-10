<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

/**
 * 根据类型获得指定目录下所有对应文件
 * @func_name     getAllFileByType
 * @param  string $dir 目录
 * @param  string $type 类型
 * @param  string $type 前缀
 * @throws DirException
 * @return 一维索引数组，值为文件绝对路径
 */
return function (string $dir, string $type= '*', string $pre = '') : array
{

    if (!is_dir($dir)) {
        throw new DirException($dir.' 文件夹不存在');
    }

    if (!is_writable($dir)) {
        throw new DirException($dir.' 文件夹无法读取');
    }

    $dir = realpath($dir).DIRECTORY_SEPARATOR;

    false === strpos($type, '.') && $type = '.'.$type;

    $files = glob($dir.$pre.'*'.$type);

    foreach (Dir::getDirList($dir, true) as $children_dir) {
        $files = array_merge($files, Dir::getAllFileByType($children_dir, $type, $pre));
    }

    return $files;
};