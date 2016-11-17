<?php declare(strict_types = 1);
namespace msqphp\main\template;

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
        // 解析变量
        $content = static::parVar($content, $data);
        // 解析数组
        $content = static::parArray($content, $data);
        // 解析函数
        $content = static::parFunc($content, $data);
        // 解析语言
        $content = static::parLanguae($content, $language);
        // 解析常量
        $content = static::parConstant($content);
        // 解析include
        return static::parInclude($content, $data, $language);
    }

    /**
     * 解析变量
     * @example         <{$name}>      -->  <?php echo $name;?>
     * @example (cache) <{$name}>      -->  value
     */
    private static function parVar(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['var'], function (array $matches) use ($data) : string {
            // 变量键
            $key = $matches[1];
            // 如果数据中存在,并且缓存
            if (isset($data[$key]) && $data[$key]['cache']) {
                (is_array($data[$key]['value']) || is_object($data[$key]['value'])) && static::exception($key.'数组或对象被当作普通变量使用');
                return (string) $data[$key]['value'];
            } else {
                return '<?php echo $'.$key.';?>';
            }
        }, $content);
    }

    /**
     * 数组解析
     * @example $array = ['name'=>'liming', age=13];
     * @example <{$array.name}>       ---->   <?php echo $array['name'];?>  -----> liming
     * @example <{$array['name']}>       ---->   <?php echo $array['name'];?>  -----> liming
     * @example <{$array.age}>        ---->   <?php echo $array['age'];?>   -----> 13
     */
    private static function parArray(string $content, array $data) : string
    {
        return preg_replace_callback([
            static::$pattern['array_a'], static::$pattern['array_b']
        ], function (array $matches) use ($data) : string {
            // 键
            $var_name = $matches[1];
            // 值
            $key = $matches[2];

            // 数组缓存
            if (isset($data[$var_name]) && $data[$var_name]['cache']) {
                // 获取真实键的数组
                $arr_key = array_map('static::textToPhpValue', false === strpos($key, '.') ? explode('][', trim($key, '[]')) : explode('.', trim($key, '.')));

                // 获取值
                $result = $data[$var_name]['value'];
                for ($i = 0, $l = count($arr_key); $i < $l; ++$i) {
                    $result = $result[$arr_key[$i]];
                }

                return $result;
            } else {
                // 拼接成php格式
                if (false !== strpos($key, '.')) {
                    $key = array_map('static::phpValueTotext', explode('.', trim($key, '.')));
                    $key = '['.implode('][', $key).']';
                }
                return '<?php echo $'.$var_name.$key.';?>';
            }
        }, $content);
    }

    /**
     * 解析函数
     * 规则:
     *     1.有参数,参数缓存,直接替换
     *     2.有参数,参数部分缓存,缓存参数替换为值
     *     3.无参数,不缓存
     * @example
     *     $a = 'test';
     *     $c = 5;
     *     原始标签           ----> 无缓存效果                  ----> 缓存效果(仅$a缓存)
     *     <{time()}>         ----> <?php echo time();?>        ----> <?php echo (string) time();?>
     *     <{substr($a, 2)}>  ----> <?php echo substr($a,2);?>  ----> 'st'
     *     <{substr($a, $c)}> ----> <?php echo substr($a,$c);?> ----> <?php echo (string) substr('test',$c);?>
     */
    private static function parFunc(string $content, array $data) : string
    {
        return preg_replace_callback(static::$pattern['func'], function (array $matches) use ($data) : string {

            // 函数名称
            $func_name = $matches[1];

            // 函数是否缓存
            $cached = null;

            // 获取函数参数
            $args = array_map('trim', explode(',', $matches[2]));

            // 为空
            if ($args === ['']) {
                $args = [];
            } else {
                // 得到参数列表,可以为空,若果参数缓存则直接替换
                $args = array_map(function (string $value) use ($data, & $cached) {
                    // 如果$开头,则为变量
                    if (isset($value[0]) && '$' === $value[0]) {
                        // 去掉$
                        $arg_name = substr($value, 1);
                        if (isset($data[$arg_name]) && $data[$arg_name]['cache']) {
                            $cached === null && $cached = true;
                            return $data[$arg_name]['value'];
                        } else {
                            $cached = false;
                            return $value;
                        }
                    } else {
                        // 返回值
                        return static::textToPhpValue($value);
                    }
                    // 参数
                }, $args);
            }

            return $cached ? (string) call_user_func_array($func_name, $args) : '<?php echo (string) '.$func_name.'('.implode(',', array_map('static::phpValueTotext',$args)).');?>';

        }, $content);
    }

    /**
     * 解析语言
     * @example <{language.username}>  =>    用户名 | username (一次解析, 直接替换)
     * @example <{lang.username}>      =>    用户名 | username (一次解析, 直接替换)
     */
    private static function parLanguae(string $content, array $language) : string
    {
        return preg_replace_callback([
            static::$pattern['language_a'], static::$pattern['language_b']
        ], function (array $matches) use ($language) : string {
            // 语言存在返回
            if (isset($language[$matches[1]])) {
                return (string) $language[$matches[1]];
            // 异常
            } else {
                static::exception($matches[1].'对应语言不存在');
            }
        }, $content);
    }

    /**
     * 解析常量
     * @example <{constant.IMAGE}>  =>    http:// image.test.com/ (一次解析, 直接替换)
     * @example <{cont.IMAGE}>      =>     http:// image.test.com/ (一次解析, 直接替换)
     */
    private static function parConstant(string $content) : string
    {
        return preg_replace_callback([
            static::$pattern['constant_a'], static::$pattern['constant_b']
        ], function (array $matches) : string {
            // 常量存在,返回
            if (defined($matches[1])) {
                return (string) constant($matches[1]);
            // 异常
            } else {
                static::exception($matches[1].'常量未定义');
            }
        }, $content);
    }

    /**
     * 解析包含文件
     * @example <{include 'file.html'}>  =>    file_get_contents(file.html);
     */
    private static function parInclude(string $content, array $data, array $language) : string
    {
        return preg_replace_callback(static::$pattern['include'], function (array $matches) : string {
            // 文件存在,载入编译并返回, 此时$file = file.html
            if (is_file($file = $matches[1])) {
                return static::commpile(base\file\File::get($file), $data, $language);
            //异常
            } else {
                static::exception($file . '模版文件不存在');
            }
        }, $content);
    }
}