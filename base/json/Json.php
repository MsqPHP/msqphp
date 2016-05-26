<?php declare(strict_types = 1);
namespace msqphp\base\json;

final class Json
{
    use traits\CallStatic;

    public static function decode(string $json)
    {
        return json_decode($json, true);
    }
    public static function encode($data) : string
    {
        return json_encode($data);
    }
}