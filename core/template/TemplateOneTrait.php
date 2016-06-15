<?php declare(strict_types = 1);
namespace msqphp\core\template;

use msqphp\base;

trait TemplateOneTrait
{
    /**
     * 解析单个标签
     *
     * @param  string $content  标签内容
     * @param  array  $data     变量数据
     * @param  array  $language 语言数据
     *
     * @return string
     */
    private static function parOne(string $content, array $data, array $language) : string
    {
        $content = static::parInclude($content, $data, $language);
        $content = static::parLanguae($content, $language);
        unset($language);
        $content = static::parVar($content, $data);
        $content = static::parArray($content, $data);
        $content = static::parFunc($content, $data);
        unset($data);
        return static::parConstant($content);
    }

    /**
     * 解析函数
     * 规则:
     *     1.有参数,参数缓存,直接替换
     *     2.有参数,参数部分缓存,缓存参数替换为值
     *     3.无参数,不缓存
     * @example
     *     $a = 'test';
     *     $c = 5;(no cache)
     *     原始标签           ----> 无缓存效果                  ----> 缓存效果
     *     <{substr($a, 2)}>  ----> <?php echo substr($a,2);?>  ----> 'st'
     *     <{time()}>         ----> <?php echo time();?>        ----> <?php echo time();?>
     *     <{substr($a, $c)}> ----> <?php echo substr($a,$c);?> ----> <?php echo substr('test',$c);?>
     */
    private static function parFunc(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['func'], function (array $matches) use ($data) {

            //函数名称
            $func_name = $matches[1];

            //函数是否缓存
            $cached = null;

            //得到参数列表,可以为空,若果参数缓存则直接替换
            $args = array_map(function (string $value) use ($data, & $cached) {
                //去掉多余空格
                $value = trim($value);

                //如果$开头,则为变量
                if ('$' === $value[0]) {
                    //去掉$
                    $arg_name = substr($value, 1);
                    if (isset($data[$arg_name]) && $data[$arg_name]['cache']) {
                        $cached === null && $cached = true;
                        return $data[$arg_name]['value'];
                    } else {
                        $cached = false;
                        return $value;
                    }
                } else {
                    //返回值
                    return static::stringToValue($value);
                }
            }, explode(',', $matches[2]));

            //缓存
            if ($cached) {
                return (string) call_user_func_array($func_name, $args);
            //不缓存
            } else {
                return '<?php echo '.$func_name.'('.implode(',', array_map('static::valueToString',$args)).');?>';
            }

        }, $content);
    }
    /**
     * 解析包含文件
     * @example <{include 'file.html'}>  =>    file_get_contents(file.html);
     */
    private static function parInclude(string $content, array $data, array $language) : string
    {
        return preg_replace_callback(static::$pattern['include'], function (array $matches) {
            if (is_file($file = $matches[1])) {
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
        return preg_replace_callback(
            [
                static::$pattern['constant_a'],
                static::$pattern['constant_b']
            ],
            function (array $matches) {
                if (defined($matches[1])) {
                    return constant($matches[1]);
                } else {
                    throw new TemplateException($matches[1].'常量未定义');
                }
            },
            $content
        );
    }
    /**
     * 解析语言
     * @example <{lang.username}>  =>    用户名 | username (一次解析, 直接替换)
     */
    private static function parLanguae(string $content, array $language) : string
    {
        return preg_replace_callback(
            [
                static::$pattern['language_a'],
                static::$pattern['language_b']
            ],
            function (array $matches) use ($language) {
                if (isset($language[$matches[1]])) {
                    return $language[$matches[1]];
                } else {
                    throw new TemplateException($matches[1].'对应语言不存在');
                }
            },
            $content
        );
    }
    /**
     * 解析变量
     * @example         <{$name}>      -->  <?php echo $name;?>
     * @example (cache) <{$name}>      -->  value
     */
    private static function parVar(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['var'], function (array $matches) use ($data) {
            $key = $matches[1];
            if (isset($data[$key]) && $data[$key]['cache']) {
                if (is_array($data[$key]['value']) || is_object($data[$key]['value'])) {
                    throw new TemplateException($key.'数组或对象被当作普通变量使用');
                } else {
                    return (string) $data[$key]['value'];
                }
            } else {
                return '<?php echo $'.$key.';?>';
            }
        }, $content);
    }
    private static function parArray(string $content, array $data) : string
    {
        return preg_replace_callback(
            [
                static::$pattern['array_a'],
                static::$pattern['array_b']
            ],
            function (array $matches) use ($data) {
                $key = $matches[1];
                $val = $matches[2];
                if (isset($data[$key]) && $data[$key]['cache']) {

                    $arr_key = array_map('static::stringToValue', false === strpos($val, '.') ? explode('][', trim($val, '[]')) : explode('.', trim($val, '.')));

                    $result = $data[$key]['value'];

                    for ($i = 0,$l = count($arr_key); $i < $l; ++$i) {
                        $result = $result[$arr_key[$i]];
                    }

                    return $result;
                } else {
                    if (false !== strpos($val, '.')) {
                        $val = array_map('static::valueToString', explode('.', trim($val, '.')));
                        $val = '['.implode('][', $val).']';
                    }
                    return '<?php echo $'.$key.$val.';?>';
                }
            },
            $content
        );
    }
}