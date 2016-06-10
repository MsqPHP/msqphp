<?php declare(strict_types = 1);
namespace msqphp\base\dir;

use msqphp\base;

/**
 * 得到目录文件大小
 * @func_name     getDirSize
 * @param   string  $path 路径
 * @param   bool    $round      是否保留整数
 * @param   bool    $unit       是否带单位
 * @throws  DirException
 * @rely on msqphp\base\number\Number::byte();
 * @return  strging|int
 */
return function (string $dir, bool $round = true, bool $unit = true) {

    if (!is_dir($dir)) {
        throw new DirException($dir.' 文件夹不存在');

    }
    if (!is_readable($dir)) {
        throw new DirException($dir.' 文件夹不可读');
    }

    $size = 0;

    foreach (Dir::getDirList($dir, true) as $children_dir) {
        $size += Dir::getSize($children_dir, false, false);
    }
    foreach (Dir::getFileList($dir, true) as $children_file) {
        $size += base\file\File::getSize($children_file, false, false);
    }

    $round && $size = round($size);

    $unit  && $size = base\number\Number::byte($size);

    return $size;
};