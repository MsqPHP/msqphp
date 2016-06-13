<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Polymorphic
{
    private static function polymorphic(array $args)
    {
        static $allowed = [
            'string'=>['string','str','miexd'],
            'array'=>['array','arr','miexd'],
            'int'=>['int','integer','number','miexd'],
            'float'=>['float','number','miexd'],
            'object'=>['object','miexd'],
            'bool'=>['bool','miexd'],
            'null'=>['null','miexd'],
        ];
        show($args);
exit;
        show(func_get_args());
        $func_args = func_get_args();
        $args_type = array_map('static::getParamType', $args);

        $polymorphic = array_shift($func_args);
        for ($i = 0, $l = count($args); $i < $l; ++$i) {
            $value = $polymorphic[$i];
            if ($l_ = count($value) === $l - 1) {
                if ($value[0] === 'else') {
                    $callback = $value[1];
                } else {
                    for ($j = 0; $j < $l_; ++$j) {
                        if (!in_array(static::getParamType($value[$j]), $allowed[$args_type[$i]])) {
                            continue 2;
                        }
                    }
                    $callback = $value[$j];
                }
                if (!is_callable($callback)) {
                    throw new TraitsException('函数不可调用');
                }
                return call_user_func_array($callback, $args);
            }
        }
    }
    private static function getParamType($arg) : string
    {
        if (is_string($arg)) {
            return 'string';
        } elseif(is_array($arg)) {
            return 'array';
        } elseif(is_int($arg)) {
            return 'int';
        } elseif(is_float($arg)) {
            return 'float';
        } elseif(is_bool($arg)) {
            return 'bool';
        } elseif(is_null($arg)) {
            return 'null';
        } elseif(is_object($arg)) {
            return 'object';
        } else {
            throw new TraitsException('未知属性');
        }
    }
}