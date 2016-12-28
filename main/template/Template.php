<?php declare(strict_types = 1);
namespace msqphp\main\template;

use msqphp\base;

final class Template
{
    // 单句解析
    use TemplateOneTrait;
    // 多句解析
    use TemplateMoreTrait;

    // 左定界符
    private static $left_delimiter = '';
    // 右定界符
    private static $right_delimiter = '';

    // 正则
    private static $pattern = [];

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new TemplateException($message);
    }

    // 模板引擎初始化
    private static function init() : void
    {
        // 初识过则返回
        static $inited = false;

        if ($inited) {
            return;
        }
        $inited      = true;

        // 获取配置
        $config  = app()->config->get('template');
        //左右定界符及正则开始部分赋值
        static::$left_delimiter  = $config['left_delimiter']  ?? '<{';
        static::$right_delimiter = $config['right_delimiter'] ?? '}>';

        static::initPattern();
    }

    // 初始化正则数据
    private static function initPattern() : void
    {
        // 正则开始
        $preg_start     = '/^'.preg_quote(static::$left_delimiter);
        // 正则结束
        $preg_end       = '\\s*'.preg_quote(static::$right_delimiter).'$/';
        // 至少一个空格
        $blank          = '\\s+';
        // 可能有空格,且数量不限
        $may_blank      = '\\s*';
        // 变量名称
        $name           = '([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)';
        // php变量
        $var            = '(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)';
        // php变量值
        $var_value      = '(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff\\[\\]\\\'\\"\\.]*)';
        // 比较符
        $compare        = '(\\<\\=|\\>=|\\<|\\<\\>|\\>|\\<\\>|\\!\\=|\\=\\=|\\=\\=\\=|\\!\\=\\=)';
        // 字符串或者整值
        $stringOrNumber = '([\'][^\']*[\']|[\\"][^\\"]*[\\"]|[0-9\.]*)';
        // 文件路径
        $file_path      = '([\\w\\/\\.:-]+)';
        // 字母和下划线组合
        $letters_and_   = '([A-Za-z_]+)';
        // 函数
        $func = $name.'\\('.'([\s\S]*)'.'\\)';

        // 一下均假设 左定界符为<{,右定界符为}>
        static::$pattern = [
            //               <{include 'file_path'}>
            'include'     => $preg_start.'include'.$blank.$file_path .$preg_end,
            //               <{constant.常量名}>
            'constant_a'  => $preg_start.'constant\\.'.$letters_and_ .$preg_end,
            //               <{cont.常量名}>
            'constant_b'  => $preg_start.'cont\\.'.$letters_and_ .$preg_end,
            //               <{language.语言名}>
            'language_a'  => $preg_start.'language\\.'.$letters_and_ .$preg_end,
            //               <{lang.语言名}>
            'language_b'  => $preg_start.'lang\\.([A-Za-z_]+)' .$preg_end,
            //               <{$变量名}>
            'var'         => $preg_start.$var.$preg_end,
            //               <{$变量名[键][键].....}>
            //               <{$变量名.键.键.....}>
            'array'     => $preg_start.$var.'([\\[\\w\'\\"\\]]+|[\\.\\w]+)'.$preg_end,
            //               <{函数名(任意值)}>
            'func'        => $preg_start.$func.$preg_end,
            //               <{foreach $array as $value}>
            'foreach_a'   => $preg_start.'foreach'.$blank.$var_value.$blank.'as'.$blank.$var.$preg_end,
            //               <{foreach $array as $key => $value}>
            'foreach_b'   => $preg_start.'foreach'.$blank.$var_value.$blank.'as'.$blank.$var.$may_blank.'\\=\\>'.$may_blank.$var.$preg_end,
            //               <{endforeach}> || <{/endforeach}>
            'foreach_end' => $preg_start.'(\\/endforeach|endforeach)'.$preg_end,
            //               <{if $变量名 比较符 $变量名}>
            'if_a'        => $preg_start.'if'.$may_blank.$var.$may_blank.$compare.$may_blank.$var.$preg_end,
            //               <{if $变量名 比较符 值}>
            'if_b'        => $preg_start.'if'.$may_blank.$var.$may_blank.$compare.$may_blank.$stringOrNumber.$preg_end,
            //               <{if 函数}>
            'if_c'        => $preg_start.'if'.$blank.$func.$may_blank.$preg_end,
            //               <{elseif $变量名 比较符 $变量名}>
            'elseif_a'    => $preg_start.'elseif'.$may_blank.$var.$may_blank.$compare.$may_blank.$var.$preg_end,
            //               <{elseif $变量名 比较符 值}>
            'elseif_b'    => $preg_start.'elseif'.$may_blank.$var.$may_blank.$compare.$may_blank.$stringOrNumber.$preg_end,
            //               <{if 函数}>
            'elseif_c'    => $preg_start.'elseif'.$blank. $func.$may_blank.$preg_end,
            //               <{else}>
            'else'        => $preg_start.'else'.$preg_end,
            //               <{endif}> || <{/endif}>
            'endif'       => $preg_start.'(\\/endif|endif)'.$preg_end,
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
        return static::parseCommpileContentArray(static::getCommpileContentArray($content, $data, $language), $data, $language);
    }
    private static function parseCommpileContentArray(array $content_array, array $data = [], array $language = []) : string
    {
        $result = '';

        $left_delimiter      = static::$left_delimiter;
        $right_delimiter     = static::$right_delimiter;
        // 当存在数据时
        while (isset($content_array[0])) {
            // 如果以左定界符开始并以右定界符结尾
            if (base\str\Str::startsWith($content_array[0], $left_delimiter) && base\str\Str::endsWith($content_array[0], $right_delimiter)) {
                $tag = $content_array[0];
                // 以定界符加foreach开始
                if (base\str\Str::startsWith($tag, $left_delimiter.'foreach')) {
                    //foreach解析
                    $result .= static::parForeach($content_array, $data, $language);
                // 以定界符加if开始
                } elseif (base\str\Str::startsWith($tag, $left_delimiter.'if')) {
                    // if解析
                    $result .= static::parIf($content_array, $data, $language);
                } else {
                    // 单个标签,直接翻译并返回
                    $result .= static::parOne($tag, $data, $language);
                    array_shift($content_array);
                }
            // 直接赋值
            } else {
                $result .= array_shift($content_array);
            }
        }

        return $result;
    }
    /**
     * 获取待编译的内容数组
     * @param   string  $content   内容数据
     * @param   array   $data      变量数据
     * @param   array   $language  语言数据
     * @return  array
     */
    private static function getCommpileContentArray(string $content, array $data = [], array $language = []) : array
    {
        // 结果数组
        $content_arr = [];
        // 左定界符
        $left_delimiter      = static::$left_delimiter;
        // 右定界符
        $right_delimiter     = static::$right_delimiter;
        // 右定界符长度
        $right_delimiter_len = strlen($right_delimiter);
        // 当有下一左定界符时
        while (false !== $left_pos = strpos($content, $left_delimiter)) {
            // 如果左定界符不在数据最前面,则将其前所有数据直接添加到结果数组中
            if ($left_pos !== 0) {
                $content_arr[] = substr($content, 0, $left_pos);
                $content       = substr($content, $left_pos);
            }
            // 获取右定界符位置
            $regith_pos = strpos($content, $right_delimiter);
            // 如果不存在,即未闭合
            $regith_pos === false && static::exception('定界符未闭合');

            // 加上右定界符长度
            $regith_pos += $right_delimiter_len;

            // 下一个左定界符位置
            $next_left_pos = strpos($content, $left_delimiter, 1);
            // 不存在取php最大值
            $next_left_pos === false && $next_left_pos = PHP_INT_MAX;

            /**
             * 右定界符大于下一个左定界符位置,即与下一个左定界符形成一个标签
             * <{ if $a = <{$a}> }>
             * ---->   <{ if $a = 解析后 }>
             * ---->   解析
             */
            while ($regith_pos > $next_left_pos) {
                // 取中间内容,并模版编译
                $middle = substr($content, $next_left_pos, $regith_pos);
                $content = str_replace($middle, static::commpile($middle,$data,$language), $content);
                // 重新获取右定界符位置
                $regith_pos = strpos($content, $right_delimiter);
                // 如果不存在,即未闭合
                $regith_pos === false && static::exception('定界符未闭合');
                // 加上右定界符长度
                $regith_pos += $right_delimiter_len;
                // 下一个左定界符位置
                $next_left_pos = strpos($content, $left_delimiter, 1);
                // 不存在取php最大值
                $next_left_pos === false && $next_left_pos = PHP_INT_MAX;
            }
            $content_arr[] = substr($content, 0, $regith_pos);
            // 赋值给结果
            $content = substr($content, $regith_pos);
        }

        // 不为空,即还有内容,赋值
        !empty($content) && $content_arr[] = $content;

        return $content_arr;
    }


    /**
     * 比较两个值的大小
     * @param   miexd   $value_a  值A
     * @param   miexd   $value_b  值B
     * @param   string  $type     比较类型
     * @return  bool
     */
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
                static::exception('未知的比较符' . var_export($type, true));
        }
    }

    /**
     * 将文本值转换为对应php值
     *
     * @param   string  $value  文本值
     *
     * @return  miexd
     */
    private static function textToPhpValue(string $value)
    {
        // 字符串
        if (isset($value[0]) && $value[0] === '\'') {
            return trim(stripslashes($value), '\'');
        } elseif (isset($value[0]) && $value[0] === '"') {
            return trim(stripslashes($value), '"');
        // 布尔值
        } elseif ($value === 'true') {
            return true;
        } elseif ($value === 'false') {
            return false;
        // 数字
        } elseif (is_numeric($value)) {
            return is_int($value) ? (int) $value : (float) $value;
        // null
        } elseif ($value === 'null') {
            return null;
        } elseif (is_string($value)) {
            return $value;
        // 未知
        } else {
            static::exception('未知的类型值' . var_export($value, true));
        }
    }

    /**
     * php值转换为texr文本
     *
     * @param   miexd  $value  php值
     *
     * @return  string
     */
    private static function phpValueTotext($value) : string
    {
        // 字符串
        if (is_string($value)) {
            if (isset($value[0]) && $value[0]=== '$') {
                return $value;
            }
            return '\'' . addslashes($value) . '\'';
        // 数字
        } elseif (is_int($value) || is_float($value)) {
            return (string) $value;
        // 布尔值
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        // null
        } elseif (is_null($value)) {
            return 'null';
        } else {
            static::exception('未知的类型值' . var_export($value, true));
        }
    }

    private static function isCachedValue(string $name, array $data) : bool
    {
        return isset($data[$name]) && $data[$name]['cache'];
    }
    private static function getArrayValue(string $name, string $key, array $data)
    {
        // 分割字符串
        $arr_key_list = false === strpos($key, '.') ? explode('][', trim($key, '[]')) : explode('.', trim($key, '.'));
        // 获取对应值
        $arr_key = array_map('static::textToPhpValue', $arr_key_list);
        // 获取值
        $result = $data[$name]['value'];

        for ($i = 0, $l = count($arr_key); $i < $l; ++$i) {
            if (!isset($result[$arr_key[$i]])) {
                return NULL;
            }
            $result = $result[$arr_key[$i]];
        }

        return $result;
    }
    private static function getArrayName(string $name, string $key) : string
    {
        // 拼接成php格式
        if (false !== strpos($key, '.')) {
            $key = array_map('static::phpValueTotext', explode('.', trim($key, '.')));
            $key = '['.implode('][', $key).']';
        }
        return '$'. $name . $key;
    }
    private static function parseFunctionWithNameAndArgsList(string $func_name, string $args_list, array $data)
    {
        // 函数是否缓存
        $cached = null;
        // 获取函数参数
        $args_list = array_map('trim', explode(',', $args_list));
        $args = [];
        // 得到参数列表,可以为空,若果参数缓存则直接替换
        for ($i = 0, $count = count($args_list); $i < $count; $i++) {
            $arg_name = $args_list[$i];
            // 不以$打头,则判断为一个简单的定值
            if (!isset($arg_name[0]) || '$' !== $arg_name[0]) {
                // 返回值
                $args[$i] = static::textToPhpValue($arg_name);
                continue;
            }
            // 真是参数名
            $true_arg_name = substr($arg_name, 1);
            // 如果为缓存值

            // 是否数组判断
            if ((false !== $pos = strpos($arg_name, '.')) || false !== $pos = strpos($arg_name, '[')) {
                $array_name = substr($arg_name, 0, $pos);
                $true_arg_name = $true_array_name = substr($array_name, 1);
                $array_name_key = substr($arg_name, $pos);
                $arg_value = static::getArrayValue($true_array_name, $array_name_key, $data);
                $arg_name = static::getArrayName($true_array_name, $array_name_key);
            } else {
                $arg_value = isset($data[$true_arg_name]) ? $data[$true_arg_name]['value'] : '';
            }

            if (static::isCachedValue($true_arg_name, $data)) {
                $cached === null && $cached = true;
                // 返回缓存值
                $args[$i] = $arg_value;
            // 返回原值
            } else {
                if (true === $cached) {
                    $cached = false;
                    $i = 0;
                } else {
                    $cached = false;
                    $args[$i] = $arg_name;
                }
            }
        }

        if (!$cached) {
            $result = $func_name.'('.implode(',', array_map('static::phpValueTotext',$args)).')';
        } else  {
            switch (strtolower($func_name)) {
                case 'isset':
                    $key = $args_list[0];
                    // 移除$
                    $key = substr($key, 1);
                    // 分割字符串
                    $key_list = false === strpos($key, '.') ? explode('][', trim($key, '[]')) : explode('.', trim($key, '.'));
                    // 获取对应值
                    $key = array_map('static::textToPhpValue', $key_list);
                    $key = implode('.', $key);
                    $result = base\arr\Arr::isset($data, $key);
                    break;
                default:
                    $result = (string) call_user_func_array($func_name, $args);
                    break;
            }
        }

        return ['cached'=>$cached, 'result'=>$result];

    }
}