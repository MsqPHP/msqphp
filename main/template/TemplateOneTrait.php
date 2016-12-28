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
            // 获得真实键;
            $true_key = substr($key, 1);
            // 如果为缓存值
            if (static::isCachedValue($true_key, $data)) {
                // 判断类型,如果为错误类型,报错
                (is_array($data[$true_key]['value']) || is_object($data[$true_key]['value'])) && static::exception($true_key.'数组或对象被当作普通变量使用');
                // 替换对应值
                return (string) $data[$true_key]['value'];
            } else {
                // 返回一个php语句
                return '<?php echo '.$key.';?>';
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
        return preg_replace_callback(static::$pattern['array'], function (array $matches) use ($data) : string {
            // 键
            $arr_name = $matches[1];
            // 真实名
            $true_arr_name = substr($arr_name, 1);
            // 值
            $key = $matches[2];

            // 如果是一个缓存值
            if (static::isCachedValue($true_arr_name, $data)) {
                return (string) static::getArrayValue($true_arr_name, $key, $data);
            } else {
                return '<?php echo '.static::getArrayName($true_arr_name, $key).';?>';
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
            // 参数列表
            $args_list = $matches[2];
            $func_info = static::parseFunctionWithNameAndArgsList($func_name, $args_list, $data);
            if ($func_info['cached']) {
                return (string) $func_info['result'];
            } else {
                return '<?php echo (string) '.$func_info['result'].';?>';
            }
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