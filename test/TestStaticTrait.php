<?php declare(strict_types = 1);
namespace msqphp\test;

trait TestStaticTrait
{
    final protected static function checkFunction($func_info, array $args = [], $result = null) : void
    {
        if (is_array($func_info)) {
            if (is_object($func_info[0])) {
                $func_name = get_class($func_info[0]) . '->' . $func_info[1];
            } elseif (is_string($func_info[0])) {
                $func_name = $func_info[0] . '::' . $func_info[1];
            } else {
                throw new TestException('测试函数为位置类型,请检查是否代码是否正确');
            }
        } elseif (is_string($func_info)) {
            $func_name = $func_info;
        } elseif (is_a($func_info, '\Closure')) {
            $func_name = '闭包函数';
        } else {
            throw new TestException('测试函数为位置类型,请检查是否代码是否正确');
        }

        $str = '函数：' . $func_name . str_repeat('&nbsp;', strlen($func_name) < 30 ? 30 - strlen($func_name) : 0);

        try {
            $func_result = call_user_func_array($func_info, $args);
        } catch (\msqphp\core\exception\Exception | \Exception $e) {
            $func_result = $e->getMessage();
        }

        if ($result === $func_result || is_a($result, '\Closure') && $result($func_result)) {
            app()->response::dump($str, '测试成功;');
        } else {
            $info = [
                $str . '测试失败;',
                '参数：',
                $args,
                '结果应为：',
                $result,
                '实际结果：',
                $func_result,
            ];

            app()->response::dumpArray($info);

            throw new TestException('测试发生错误,请检验代码');
        }

    }
    final protected static function testFunction($function, array $args = [], $result = null) : void
    {
        static::checkFunction($function, $args, $result);
    }
    final protected static function testObjectMethod($obj, string $method, array $args = [], $result = null) : void
    {
        static::checkFunction([$obj, $method], $args, $result);
    }

    final protected static function testObjectProperty($obj, string $property, $value) : void
    {
        if ($obj->$property === $value) {
            app()->response::dump('属性:'.get_class($obj).'->$'.$property, '测试成功');
        } else {
            app()->response::dump('属性:'.get_class($obj).'->$'.$property, '测试失败');
            app()->response::dump('结果应为：');
            app()->response::dump($value);
            app()->response::dump('实际结果：');
            app()->response::dump($obj->$property);
            throw new TestException('测试发生错误,请检验代码');
        }
    }
    final protected static function testClassStaticMethod(string $class, string $method, array $args = [], $result = null) : void
    {
        static::checkFunction([$class, $method], $args, $result);
    }

    final protected static function testClassStaticProperty(string $class, string $property, $value) : void
    {
        if ($class::$property === $value) {
            app()->response::dump('属性:'.get_class($class).'::$'.$property, '测试成功');
        } else {
            app()->response::dump('属性:'.get_class($class).'::$'.$property, '测试失败');
            app()->response::dump('结果应为：');
            app()->response::dump($value);
            app()->response::dump('实际结果：');
            app()->response::dump($class::$property);
            throw new TestException('测试发生错误,请检验代码');
        }
    }
}