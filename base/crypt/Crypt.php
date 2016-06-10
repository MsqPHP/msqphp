<?php declare(strict_types = 1);
namespace msqphp\base\crypt;

use msqphp\base;
use msqphp\traits;

final class Crypt
{
    use traits\CallStatic;

    public static function base64_encode(string $str) : string
    {
        return base64_encode($str);
    }
    public static function base64_decode(string $str) : string
    {
        return base64_decode($str);
    }
    public static function urlencode(string $str) : string
    {
        return urlencode($str);
    }
    public static function urldecode(string $str) : string
    {
        return urldecode($str);
    }
    public static function mcrypt_decrypt()
    {

    }
    public static function mcrypt_encrypt()
    {

    }
    public static function encode(string $str) : string
    {
        return $str;
    }
    public static function decode(string $str) : string
    {
        return $str;
    }
}