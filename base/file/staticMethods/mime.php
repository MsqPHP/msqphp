<?php declare(strict_types = 1);
namespace msqphp\base\file;

/**
 * 获得文件mime
 * @func_name  mime
 * @param   string $file 文件名
 * @return  string
 */
return function (string $file) : string {
    if (!is_file($file)) {
        throw new FileException($file.'文件不存在');
    }
    if (!function_exists('finfo_open')) {
        throw new FileException('需要php_fileinfo扩展');
    }
    $finfo    = finfo_open(FILEINFO_MIME);
    $mime = finfo_file($finfo, $filename);
    finfo_close($finfo);

    return $mime;
};