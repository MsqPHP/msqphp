<?php declare(strict_types = 1);
namespace Msqphp\Base\Crypt;

class Crypt
{
    public static function crypt($value,$solt)
    {
    }
    public static function deCrypt($value, $solt)
    {
    }
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
}