<?php declare(strict_types = 1);
namespace msqphp\core\response;

use msqphp\base;

trait ResponseDumpTrait
{
    public static $type = null;
    /**
     * @param  miexd        $data 数据
     * @param  string       $root 根节点
     */


    // xml格式返回
    public static function xml($data, string $root = 'root', bool $exit = true) : void
    {
        static::type('xml');

        echo '<?xml version="1.0" encoding="utf-8"?><', $root, '>', base\xml\Xml::encode($data), '</', $root, '>';

        $exit && exit;
    }
    // json格式返回
    public static function json($data, bool $exit = true) : void
    {
        static::type('json');

        echo base\json\Json::encode($data);

        $exit && exit;
    }

    private static function type(string $type)
    {
        if (null === static::$type) {
            base\header\Header::type($type);
        } else {
            $type === static::$type || static::exception('回复的页面格式不准确,包含'.$type.'和'.static::$type);
        }
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
    public static function dumpHtmlFile(string $file, array $data, bool $return)
    {
        static::type('html');
        return static::dumpFile($file, $data, $return);
    }
    public static function dumpHtmlFiles(array $files, array $data, bool $return)
    {
        static::type('html');
        return static::dumpFiles($file, $data, $return);
    }
    public static function dumpFiles(array $files, array $data, bool $return)
    {
        if ($return) {
            $result = '';
            foreach ($files as $file) {
                $result .= static::dumpFile($file, $data, $return);
            }
            return $result;
        } else {
            foreach ($files as $file) {
                static::dumpFile($file, $data, $return);
            }
        }
    }
    public static function dumpFile(string $____file, array $____data, bool $____return)
    {
        is_file($____file) || static::exception($____file.'文件不存在,无法输出');
        // 打散
        extract($____data, EXTR_OVERWRITE);
        // 静态则ob, 否则直接require
        if ($____return) {
            ob_start();
            ob_implicit_flush(0);
            require $____file;
            return ob_get_flush();
        } else {
            require $____file;
        }
    }
}