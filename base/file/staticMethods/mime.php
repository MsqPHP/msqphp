<?php declare(strict_types = 1);
namespace msqphp\base\file;

function mime(string $file) : string
{
    if (!is_file($file)) {
        throw new FileException($file.'不存在');
    }
    if (!is_readable($file)) {
        throw new FileException($file.'不可读');
    }
    if (!function_exists('finfo_open')) {
        throw new FileException('需要php_fileinfo扩展');
    }
    $finfo    = finfo_open(FILEINFO_MIME);
    $mime = finfo_file($finfo, $filename);
    finfo_close($finfo);

    return $mime;
}