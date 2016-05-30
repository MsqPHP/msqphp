<?php declare(strict_types = 1);
namespace msqphp\core\http;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Http
{
    use traits\get;

    public static function download(string $file)
    {
        if (!is_file($file) || !is_writable($file)) {
            throw new HeaderException($file.'不存在或不可读');
        }
        base\header\Header::download($file);
        readfile($file);
        exit;
    }
}