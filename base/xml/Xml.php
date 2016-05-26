<?php declare(strict_types = 1);
namespace msqphp\base\xml;

use msqphp\base;
use msqphp\traits;

final class Xml
{
    use traits\CallStatic;

    public static function encode(array $data, string $tag = 'item', bool $id = false) : string
    {
        $xml = '';

        foreach ($data as $key => $value) {
            $xml .= '<'.( is_numeric($key) ? ( $id ? $tag . ' id="'.$key.'"' : $tag ) : $key ).'>' ;

            $xml .= (is_array($value) || is_object($value)) ? static::encoding($data, $tag, $id) : $value;

            $xml .= '</'.( is_numeric($key) ? $tag : $key ).'>';

        }

        return $xml;
    }

    public static function decode(string $xml)
    {
        $data = '';
        return $data;
    }
}