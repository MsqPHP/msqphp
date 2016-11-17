<?php declare(strict_types = 1);
namespace msqphp\core\vendor;

final class Http
{
    private function exception(string $message) : void
    {
        throw new HeaderException($message);
    }

    /**
     * 发出下载文件网址
     *
     * @param   string  $file  文件路径
     *
     * @return  void
     */
    public static function download(string $file) : void
    {
        (!is_file($file) || !is_writable($file)) && static::exception($file.'不存在或不可读');

        base\header\Header::download($file);

        readfile($file);
    }
}