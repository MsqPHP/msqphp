<?php declare(strict_types = 1);
namespace msqphp\core\response;

use msqphp\base;

trait ResponseDumpTrait
{
    /**
     * @param  miexd        $data 数据
     * @param  string       $root 根节点
     */


    // xml格式返回
    public static function xml($data, string $root = 'root') : void
    {
        base\header\Header::type('xml');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';

        $xml = '<'.$root.'>'.base\xml\Xml::encode($data).'</'.$root.'>';

        echo $xml;
    }
    // json格式返回
    public static function json($data) : void
    {
        base\header\Header::type('json');

        echo base\json\Json::encode($data);
    }

    public static function dump() : void
    {
        if (\msqphp\Environment::getRunMode() === 'cli') {
            array_map(function ($v) {
                var_export($v);
            }, func_get_args());
            echo PHP_EOL;
        } else {
            echo '<pre>';

            array_map(function ($v) {
                var_export(base\filter\Filter::html($v));
            }, func_get_args());

            echo '</pre><hr/>';
        }
    }
    public static function dumpArray(array $data) : void
    {
        foreach ($data as $value) {
            static::dump($value);
        }
    }
}