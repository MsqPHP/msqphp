<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Polymorphic
{
    private static function polymorphic()
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
        $func_args = func_get_args();
        $args = array_shift($func_args);
        $args_type = array_map('static::getParamType', $args);
        $args_len = count($args);

        while (isset($func_args[0])) {
            $callback = array_pop($func_args[0]);
            $type = $func_args[0] ?: [];
            if (count($type) === $args_len) {
                for ($i = 0, $l = count($type); $i < $l; ++$i) {
                    if (!in_array(strtolower($type[$i]), $allowed[$args_type[$i]])) {
                        continue 2;
                    }
                }

                if (!is_callable($callback)) {
                    throw new TraitsException('函数不可调用');
                }
                return call_user_func_array($callback, $args);
            }
            array_shift($func_args);
        }

        throw new TraitsException('函数不可重构');

        for ($i = 0, $l = count($args); $i < $l; ++$i) {
            $value = $func_args[$i];
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