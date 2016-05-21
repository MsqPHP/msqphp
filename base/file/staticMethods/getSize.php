<?php declare(strict_types = 1);
namespace msqphp\base\file;

use msqphp\base;
/**
 * 得到文件大小
 * @func_name     getFileSize
 * @param   string $path 路径
 * @param   bool    $round      是否保留整数
 * @param   bool    $unit       是否带单位
 * @rely on msqphp\base\number\Number::byte();
 * @throws  FileException
 * @return  strging|int
 */
return function (string $file, bool $round = true, bool $unit = true)
{
    if (!is_file($file)) {
        throw new FileException($file.' 文件不存在');
    }
    if (!is_readable($file)) {
        throw new FileException($file.' 文件不可读');
    }
    //获得字节大小
    $size = filesize($path);

    $round && $size = round($size);

    $unit  && $size = base\number\Number::byte($size);

    return $size;
};