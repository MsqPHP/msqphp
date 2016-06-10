<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

/**
 * 获得一个目录映射
 * @param  string      $dir  目录
 * @param  int|integer $deep 深度
 * @return array
 */
return function (string $dir, int $deep = 100000000) : array {
    if ($deep === 0) {
        return [];
    }
    if (!is_dir($dir)) {
        throw new DirException($dir.'不存在');
    }
    if (!is_readable($dir)) {
        throw new DirException($dir.'不可读');
    }
    $map = [];
    $dir = realpath($dir) . DIRECTORY_SEPARATOR;
    foreach (static::getDirList($dir, false) as $children_dir) {
        $map[$children_dir] = static::map($dir.$children_dir, $deep - 1);
    }
    foreach (static::getFileList($dir, false) as $children_file) {
        $map[] = $children_file;
    }
    return $map;
};