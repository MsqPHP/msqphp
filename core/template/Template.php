<?php declare(strict_types = 1);
namespace msqphp\core\template;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Template
{
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
        $name      = '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)';
        $var       = '\\$'.$name;
        $compare   = '(\\<\\=|\\>=|\\<|\\<\\>|\\>|\\<\\>|\\!\\=|\\=\\=|\\=\\=\\=|\\!\\=\\=)';

        static::$pattern = [
            'include'     => [ 'pattern' => $left.'include'.$blank.'([\\w\\/\\.:-]+)'.$right ],
            'constant_a'  => [ 'pattern' => $left.'constant\\.([A-Za-z_]+)'.$right ],
            'constant_b'  => [ 'pattern' => $left.'cont\\.([A-Za-z_]+)'.$right ],
            'language_a'  => [ 'pattern' => $left.'language\\.([A-Za-z_]+)'.$right ],
            'language_b'  => [ 'pattern' => $left.'lang\\.([A-Za-z_]+)'.$right ],
            'var'         => [ 'pattern' => $left.$var.$right ],
            'array_a'     => [ 'pattern' => $left.$var.'([\\[\\w\'\\"\\]]+)'.$right ],
            'array_b'     => [ 'pattern' => $left.$var.'([\\.\\w]+)'.$right ],
            'func'        => [ 'pattern' => $left.$name.'\\('.'([\s\S]*)'.'\\)'.$right ],
            //形式A       foreach $array as $value
            'foreach_a'   => [
                                'pattern'=>$left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.$right,
                                'replace'=>''
                            ],
            //形式B       foreach $array as $key => $value
            'foreach_b'   => [
                                'pattern'=>$left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.$may_blank.'\\=\\>'.$may_blank.$var.$right,
                                'replace'=>''
                            ],
            //正则
            'foreach_end' => [
                                'pattern'=>$left.'(\\/endforeach|endforeach)'.$right,
                                'replace'=>''
                            ],
            'if_a'        => [
                                'pattern'=>$left.'if'.$blank.$var.$may_blank.$compare.$may_blank.$var.$right,
                                'replace'=>'<?php if(\\$\\1\\2$\\3) : ?>'
                            ],
            'if_b'        => [
                                'pattern'=>$left.'if'.$blank.$var.$may_blank.$compare.$may_blank.'([\'][^\']*[\']|[\\"][^\\"]*[\\"]|[0-9])'.$right,
                                'replace'=>'<?php if(\\$\\1\\2\\3) : ?>'
                            ],
            'elseif_a'    => [
                                'pattern'=>$left.'elseif'.$blank.$var.$may_blank.$compare.$may_blank.$var.$right,
                                'replace'=>'<?php elseif(\$\\1\\2$\\3) : ?>'
                            ],
            'elseif_b'    => [
                                'pattern'=>$left.'elseif'.$blank.$var.$may_blank.$compare.$may_blank.'([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])'.$right,
                                'replace'=>'<?php elseif(\$\\1\\2\\3) : ?>'
                            ],
            'else'        => [
                                'pattern'=>$left.'else'.$right,
                                'replace'=>'<?php else : ?>',
                            ],
            'endif'       => [
                                'pattern'=>$left.'(\/endif|endif)'.$right,
                                'replace'=>'<?php endif;?>',
                            ],
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
                    $deep = 1;

                    $begin = $content_arr[0];
                    $middle = '';

                    for ($i = 1, $l = count($content_arr); $i < $l; ++$i) {
                        if (base\str\Str::startsWith($content_arr[$i],$left_delim.'foreach')) {
                            ++$deep;
                        }
                        if (base\str\Str::startsWith($content_arr[$i],[$left_delim.'endforeach', $left_delim.'/endforeach'])) {
                            --$deep;
                        }
                        if ($deep === 0) {
                            break;
                        }
                        $middle .= $content_arr[$i];
                    }

                    if (0 !== $deep) {
                        throw new TemplateException('未闭合的foreach标签');
                    }

                    if (0 !== preg_match(static::$pattern['foreach_a']['pattern'], $begin, $foreach)) {
                        $type = 'a';
                    } elseif(0 !== preg_match(static::$pattern['foreach_b']['pattern'], $begin, $foreach)) {
                        $type = 'b';
                    } else {
                        throw new TemplateException('错误的foreach语法');
                    }

                    if (isset($data[$foreach[1]]) && $data[$foreach[1]]['cache']) {
                        foreach ($data[$foreach[1]]['value'] as $key => $value) {
                            $mid_data = array_merge($data,
                                $type === 'a'
                                ? [$foreach[2]=>['cache'=>true, 'value'=>$value]]
                                : [$foreach[2]=>['cache'=>true, 'value'=>$key], $foreach[3]=>['cache'=>true, 'value'=>$value]]
                            );
                            $result .= static::commpile($middle, $mid_data, $language);
                        }
                    } else {
                        $result .= $type === 'a' ? '<?php foreach $'.$foreach[1].' as '.$foreach[2].': ?>' : '<?php foreach $'.$data_key.' as '.$foreach[2].'=>'.$foreach[3].' : ?>';
                        $result .= static::commpile($middle, $data, $language);
                        $result .= '<?php endforeach;?>';
                    }
                    unset($middle);
                    unset($begin);
                    for (; $i >= 0; --$i) {
                        array_shift($content_arr);
                    }

                } elseif (base\str\Str::startsWith($tag, $left_delim.'if')) {
                    throw new TemplateException('待完善');
                    $deep            = 1;
                    $branch          = 0;
                    $begin           = [];
                    $begin[$branch]  = $content_arr[0];
                    $middle          = [];
                    $middle[$branch] = '';
                    for ($i = 1, $l = count($content_arr); $i < $l; ++$i) {
                        if (base\str\Str::startsWith($content_arr[$i],$left_delim.'if')) {
                            ++$deep;
                        }
                        if (base\str\Str::startsWith($content_arr[$i],[$left_delim.'endif', $left_delim.'/endif'])) {
                            --$deep;
                        }
                        if (0 === $deep) {
                            break;
                        }
                        if (1 === $deep && base\str\Str::startsWith($content_arr[$i],$left_delim.'else')) {
                            $begin[$branch] = $content_arr[$i];
                            $middle[$branch] = '';
                            ++$branch;
                            continue;
                        } else {
                            $middle[$branch] .= $content_arr[$i];
                        }
                    }

                    $if_result = '';
                    $if_cached = false;
                    if (0 !== preg_match(static::$pattern['if_a']['pattern'], $begin[0], $if)) {
                        if (isset($data[$if[1]])) {
                            if ($data[$if[1]]['cache']) {
                                $if[1] = $data[$if[1]]['value'];
                            }
                        }
                        if (isset($data[$if[1]]) && $data[$if[1]]['cache'] && isset($data[$if[3]]) && $data[$if[3]]['cache']) {
                            $value_a = $data[$if[1]]['value'];
                            $compare = $if[2];
                            $value_b = $data[$if[3]]['value'];
                            $if_cached = static::compare($value_a, $value_b, $compare);
                            if ($if_cached) {
                                $if_result = static::commpile($middle[0], $data, $language);
                            } else {
                                $if_result.= '<?php if($'.$value_a.$compare.$value_b.') : ?>';
                                $if_result.= static::commpile($middle[0], $data, $language);
                            }
                        } else {
                            $if_result.= '<?php if($'.$if[1].$if[2].'$'.$if[3].') : ?>';
                            $if_result.= static::commpile($middle[0], $data, $language);
                        }
                    } elseif(0 !== preg_match(static::$pattern['if_b']['pattern'], $begin[0], $if)) {
                        if (isset( $data[$if[1]] ) && $data[$if[1]]['cache']) {
                            $value_a = $data[$if[1]]['value'];
                            $compare = $if[2];
                            $value_b = static::getValue($if[3]);
                            $if_cached = static::compare($value_a, $value_b, $compare);
                            if ($if_cached) {
                                $if_result = static::commpile($middle[0], $data, $language);
                            } else {
                                $if_result.= '<?php if($'.$value_a.$compare.$value_b.') : ?>';
                                $if_result.= static::commpile($middle[0], $data, $language);
                            }
                        } else {
                            $if_result.= '<?php if($'.$if[1].$if[2].$if[3].') : ?>';
                            $if_result.= static::commpile($middle[0], $data, $language);
                        }
                    } else {
                        throw new TemplateException('错误的if语法');
                    }

                    for ($j = 1; $j < $branch; ++$j) {
                        if ($if_cached) {
                            break;
                        }
                        $if_begin = $begin[$branch];
                        $if_content = $middle[$branch];

                        if (0 !== preg_match(static::$pattern['else']['pattern'], $if_begin, $if)) {
                            $if_result .= static::commpile($middle[$j], $data, $language);
                        } elseif (0 !== preg_match(static::$pattern['elseif_a']['pattern'], $if_begin, $if)) {

                        } elseif(0 !== preg_match(static::$pattern['elseif_b']['pattern'], $if_begin, $if)) {
                            if (isset( $data[$if[1]] ) && $data[$if[1]]['cache']) {
                                $if_result.= static::commpile($middle[$j], $data, $language);
                            } else {
                                $if_result.= '<?php elseif($'.$if[1].$if[2].$if[3].') : ?>';
                                $if_result.= static::commpile($middle[$j], $data, $language);
                            }
                        } else {
                            throw new TemplateException('错误的if语法');
                        }
                    }
                    !$if_cached && $if_result .= '<?php endif;?>';
                    $result .= $if_result;

                    unset($if_result);
                    unset($middle);
                    unset($begin);
                    for (; $i >= 0; --$i) {
                        array_shift($content_arr);
                    }
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
    private static function parOne(string $content, array $data, array $language = []) : string
    {
        $content = static::parInclude($content, $data, $language);
        $content = static::parConstant($content);
        $content = static::parLanguae($content, $language);
        $content = static::parVar($content, $data);
        $content = static::parArray($content, $data);
        return static::parFunc($content, $data);
    }
    private static function parFunc(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['func']['pattern'], function($matches) use ($data) {
            $func_content = substr($matches[0], strlen(static::$left_delim), strlen($matches[0]) - strlen(static::$right_delim) - strlen(static::$left_delim));
            $func_name = $matches[1];
            $cached = [];
            $args = array_map(function ($value) use ($data, & $cache_arg) {
                $value = trim($value);
                if ('$' === $value[0]) {
                    $arg_name = ltrim($value, '$');
                    if (isset($data[$arg_name]) && $data[$arg_name]['cache']) {
                        return $data[$arg_name]['value'];
                    } else {
                        $cache_arg['cached'] = false;
                        return $value;
                    }
                } else {
                    return static::getValue($value);
                }
            }, explode(',', $matches[2]));


            if (isset($cache_arg['cached']) && false === $cache_arg['cached']) {
                return '<?php echo '.$func_name.'('.implode(',', array_map('static::setValue',$args)).');?>';
            } else {
                $result = call_user_func_array($func_name, $args);
                if (is_string($result) || is_int($result) || is_float($result)) {
                    return (string) $result;
                } else {
                    throw new TemplateException('错误的模版返回值');
                }
            }
        }, $content);
    }
    /**
     * 解析包含文件
     * @example <{include 'file.html'}>  =>    file_get_contents(file.html);
     */
    private static function parInclude(string $content, array $data, array $language) : string
    {
        return preg_replace_callback(static::$pattern['include']['pattern'], function($matches){
            $file = $matches[1];
            if (is_file($file)) {
                return static::commpile(base\file\File::get($file), $data, $language);
            } else {
                throw new TemplateException($file.'模版文件不存在');
            }
        }, $content);
    }
    /**
     * 解析常量
     * @example <{constant.IMAGE}>  =>    http://image.test.com/ (一次解析, 直接替换)
     */
    private static function parConstant(string $content) : string
    {
        return preg_replace_callback([
            static::$pattern['constant_a']['pattern'],
            static::$pattern['constant_b']['pattern']
        ], function($matches) {
            if (defined($matches[1])) {
                return constant($matches[1]);
            } else {
                throw new TemplateException($matches[1].'常量未定义');
            }
        }, $content);
    }
    /**
     * 解析语言
     * @example <{lang.username}>  =>    用户名 | username (一次解析, 直接替换)
     */
    private static function parLanguae(string $content, array $language) : string
    {
        return preg_replace_callback([
            static::$pattern['language_a']['pattern'],
            static::$pattern['language_b']['pattern']
        ], function($matches) use ($language) {
            if (isset($language[$matches[1]])) {
                return $language[$matches[1]];
            } else {
                throw new TemplateException($matches[1].'对应语言不存在');
            }
        }, $content);
    }
    /**
     * 解析变量
     * @example         <{$name}>      -->  <?php echo $name;?>
     * @example (cache) <{$name}>      -->  value
     */
    private static function parVar(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['var']['pattern'], function($matches) use ($data) {
            $key = $matches[1];
            if (isset($data[$key]) && $data[$key]['cache']) {
                if (is_array($data[$key]['value'])) {
                    throw new TemplateException($key.'数组被当作普通变量使用');
                } else {
                    return $data[$key]['value'];
                }
            } else {
                return '<?php echo $'.$key.';?>';
            }
        }, $content);
    }
    private static function parArray(string $content, array $data) : string
    {
        return preg_replace_callback([
            static::$pattern['array_a']['pattern'],
            static::$pattern['array_b']['pattern']
        ], function($matches) use ($data) {
            $key = $matches[1];
            $val = $matches[2];
            if (isset($data[$key]) && $data[$key]['cache']) {

                $arr_key = array_map('static::getValue', false === strpos($val, '.') ? explode('][', trim($val, '[]')) : explode('.', trim($val, '.')));

                $result = $data[$key]['value'];

                for ($i = 0,$l = count($arr_key); $i < $l; ++$i) {
                    $result = $result[$arr_key[$i]];
                }

                return $result;
            } else {
                if (false !== strpos($val, '.')) {
                    $val = array_map('static::setValue', explode('.', trim($val, '.')));
                    $val = '['.implode('][', $val).']';
                }
                return '<?php echo $'.$key.$val.';?>';
            }
        }, $content);
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
    private static function getValue($value)
    {
        $value = trim($value, '\'"');
        return 0 !== preg_match('/^\d+$/', $value) ? (int) $value : $value;
    }
    private static function filterValue($value)
    {
        return 0 !== preg_match('/^\d+$/', $value) ? (int) $value : $value;
    }
    private static function setValue($value)
    {
        if (is_int($value) || '$' === $value[0]) {
            return $value;
        } else {
            return 0 !== preg_match('/^\d+$/', $value) ? (int) $value : '\''.$value.'\'';
        }
    }
}