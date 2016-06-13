<?php declare(strict_types = 1);
namespace msqphp\core\template;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Template
{
    use TemplateOneTrait;
    use TemplateMoreTrait;

    private static $left = '';
    private static $right = '';
    private static $left_delim = '';
    private static $right_delim = '';

    private static $pattern = [];

    private static function init()
    {
        static $inited = false;
        if ($inited) {
            return;
        }
        $inited      = true;
        $config              = core\config\Config::get('template');
        static::$left_delim  = $config['left_delim'] ?? '<{';
        static::$right_delim = $config['right_delim'] ?? '<{';
        static::$left        = $left = '/^'.static::$left_delim;
        static::$right       = $right = '\\s*'.static::$right_delim.'$/';

        $blank     = '\\s+';
        $may_blank = '\\s*';
        $name      = '([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)';
        $var       = '\\$'.$name;
        $compare   = '(\\<\\=|\\>=|\\<|\\<\\>|\\>|\\<\\>|\\!\\=|\\=\\=|\\=\\=\\=|\\!\\=\\=)';
        $stringOrNumber = '([\'][^\']*[\']|[\\"][^\\"]*[\\"]|[0-9]*)';

        static::$pattern = [
            'include'     => $left.'include'.$blank.'([\\w\\/\\.:-]+)'.$right,
            'constant_a'  => $left.'constant\\.([A-Za-z_]+)'.$right,
            'constant_b'  => $left.'cont\\.([A-Za-z_]+)'.$right,
            'language_a'  => $left.'language\\.([A-Za-z_]+)'.$right,
            'language_b'  => $left.'lang\\.([A-Za-z_]+)'.$right,
            'var'         => $left.$var.$right,
            'array_a'     => $left.$var.'([\\[\\w\'\\"\\]]+)'.$right,
            'array_b'     => $left.$var.'([\\.\\w]+)'.$right,
            'func'        => $left.$name.'\\('.'([\s\S]*)'.'\\)'.$right,
            //形式A       foreach $array as $value
            'foreach_a'   => $left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.$right,
            //形式B       foreach $array as $key => $value
            'foreach_b'   => $left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.$may_blank.'\\=\\>'.$may_blank.$var.$right,
            //正则
            'foreach_end' => $left.'(\\/endforeach|endforeach)'.$right,
            'if_a'        => $left.'if'.$may_blank.$var.$may_blank.$compare.$may_blank.$var.$right,
            'if_b'        => $left.'if'.$may_blank.$var.$may_blank.$compare.$may_blank.$stringOrNumber.$right,
            'elseif_a'    => $left.'elseif'.$may_blank.$var.$may_blank.$compare.$may_blank.$var.$right,
            'elseif_b'    => $left.'elseif'.$may_blank.$var.$may_blank.$compare.$may_blank.$stringOrNumber.$right,
            'else'        => $left.'else'.$right,
            'endif'       => $left.'(\\/endif|endif)'.$right,
        ];

    }
    /**
     * 模版编译
     *
     * @param  string $content  模版内容
     * @param  array  $data     模版变量 ['name'=>'cache'=>bool,'value'=>miexd,...]
     * @param  array  $language 语言变量 ['name'=>'value',....]
     *
     * @return string
     */
    public static function commpile(string $content, array $data = [], array $language = []) : string
    {
        static::init();


        $left_delim = static::$left_delim;
        $right_delim = static::$right_delim;
        $right_delim_len = strlen($right_delim);


        $content_arr = [];


        while (false !== $start = strpos($content, $left_delim)) {
            if ($start !== 0) {
                $content_arr[] = substr($content, 0, $start);
                $content = substr($content, $start);
            }
            $end = strpos($content, $right_delim);
            $middle_pos = strpos($content, $left_delim, 1);
            $middle_pos === false && $middle_pos = PHP_INT_MAX;
            while ($end > $middle_pos) {
                show($content);

                $middle = substr($content, $middle_pos, $end + $right_delim_len);
                show($middle);
                $content = str_replace($middle, static::commpile($middle,$data,$language), $content);
                show($content);
                $end = strpos($content, $right_delim);
                $middle_pos = strpos($content, $left_delim, 1);
                $middle_pos === false && $middle_pos = PHP_INT_MAX;
            }
            $content_arr[] = substr($content, 0, $end + $right_delim_len);
            $content = substr($content, $end + $right_delim_len);
        }
        !empty($content) && $content_arr[] = $content;

        $result = '';

        while (isset($content_arr[0])) {
            if (base\str\Str::startsWith($content_arr[0], $left_delim) && base\str\Str::endsWith($content_arr[0], $right_delim)) {
                $tag = $content_arr[0];
                if (base\str\Str::startsWith($tag, $left_delim.'foreach')) {
                    $result .= static::parForeach($content_arr, $data, $language);
                } elseif (base\str\Str::startsWith($tag, $left_delim.'if')) {
                    $result .= static::parIf($content_arr, $data, $language);
                } else {
                    $result .= static::parOne($tag, $data, $language);
                    array_shift($content_arr);
                }
            } else {
                $result .= $content_arr[0];
                array_shift($content_arr);
            }
        }

        return $result;
    }
    private static function toValue(string $value)
    {
        if ($value[0] === '\'') {
            return trim($value, '\'');
        } elseif ($value[0] === '"') {
            return trim($value, '"');
        } elseif ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        } else {
            return int($value);
        }
    }
    private static function compare($value_a, $value_b, string $type) : bool
    {
        switch ($type) {
            case '===':
                return $value_a === $value_b;
            case '==':
                return $value_a ==  $value_b;
            case '!=':
            case '<>':
                return $value_a !=  $value_b;
            case '!==':
                return $value_a !== $value_b;
            case '>=':
                return $value_a >=  $value_b;
            case '<=':
                return $value_a <=  $value_b;
            case '<':
                return $value_a <   $value_b;
            case '>':
                return $value_a >   $value_b;
            default:
                throw new TemplateException('未知的比较符号');
        }
    }
    private static function stringToValue($value)
    {
        return $value[0] === '\'' || $value[0] === '"' ? substr($value, 1, strlen($value) -2) : (int)$value;
    }
    private static function filterValue($value)
    {
        return 0 !== preg_match('/^\d+$/', $value) ? (int) $value : $value;
    }
    private static function valueToString($value)
    {
        if (is_int($value) || '$' === $value[0]) {
            return $value;
        } else {
            return 0 !== preg_match('/^\d+$/', $value) ? (int) $value : '\''.$value.'\'';
        }
    }
}