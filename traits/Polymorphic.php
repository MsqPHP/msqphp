<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Polymorphic
{
    private static function polymorphic()
    {
        static $allowed = [
            'string'=>['string','str','miexd'],
            'array'=>['array','arr','miexd'],
            'integer'=>['int','integer','number','miexd'],
            'double'=>['float','double','number','miexd'],
            'object'=>['object','miexd'],
            'boolean'=>['bool','boolean','miexd'],
            'NULL'=>['null','miexd'],
            'resource'=>['resource','miexd']
        ];

        //所有参数
        $func_args = func_get_args();

        //第一个为传参
        $args = array_shift($func_args);

        //参数类型
        $args_type = array_map('gettype', $args);
        //参数个数
        $args_len = count($args);

        //匹配
        while (isset($func_args[0])) {
            //最后一个为callback
            $callback = array_pop($func_args[0]);
            //其余为参数类型
            $type = $func_args[0] ?: [];
            //如果相等,即个数相同
            if (count($type) === $args_len) {
                //类型检测
                for ($i = 0, $l = count($type); $i < $l; ++$i) {
                    if ('miexd' === strtolower($type[$i]) || !in_array(strtolower($type[$i]), $allowed[$args_type[$i]])) {
                        continue 2;
                    }
                }

                if (!is_callable($callback)) {
                    throw new TraitsException('函数不可重构');
                }
                return call_user_func_array($callback, $args);
            }

            //匹配失败,删除
            array_shift($func_args);
        }

        throw new TraitsException('函数不可重构');
    }
}