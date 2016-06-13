<?php declare(strict_types = 1);
namespace msqphp\test;

use msqphp\base;

abstract class Test
{
    use TestPointerTrait;
    protected $not_test = ['test', 'testProperty', 'testStaticProperty', 'testStart', 'testFunc', 'testMethod', 'testAll', 'testThis', 'testStaticMethod', 'testFunction'];

    public function testStart()
    {
        echo '测试文件正常运行，但未定义测试函数';
        echo '<hr/>';
        echo '应该且必须在该类中添加', __FUNCTION__, '函数，并调用相关方法';
        echo '<hr/>';
        echo '至少应该添加 $this->init();$this->testThis()), 测试该文件中所有test开头的函数';
        echo '<hr/>';
        echo '传参为null，返回值为true（错误有提示）';
        echo '<hr/>';
    }

    protected function testThis()
    {
        foreach ($this->getMethods($this) as $method) {
            static::testMethod($this, $method, [], null);
        }
    }
    protected static function testFunc($func, array $args = [], $result=null)
    {

        if (is_object($func[0])) {
            $class_name = get_class($func[0]);
            $method_name = $func[1];
            $func_str = $class_name.'->'.$method_name;
        } elseif(is_array($func)) {
            $class_name = $func[0];
            $method_name = $func[0];
            $func_str = $class_name.'->'.$method_name;
        } elseif(is_string($func)) {
            $class_name = $func;
            $method_name = '';
            $func_str = $class_name;
        } else {
            throw new TestException('未知类型');
        }


        $white_len =  strlen($func_str) < 30 ? 30 - strlen($func_str) : 0;

        $str = '函数：' . $func_str . str_repeat('&nbsp;', $white_len);

        $args = (array) $args;

        try {
            $func_result = call_user_func_array($func, $args);

        } catch (\msqphp\core\exception\Exception $e) {

            $func_result = $e->getMessage();
        }

        if ($result === $func_result || is_object($result) && $result($func_result)) {

            base\response\Response::dump($str, '测试成功;');

        } else {

            base\response\Response::dump($str, '测试失败;');
            base\response\Response::dump('参数：');
            base\response\Response::dump($args);
            base\response\Response::dump('结果应为：');
            base\response\Response::dump($result);
            base\response\Response::dump('实际结果：');
            base\response\Response::dump($func_result);

            throw new TestException("测试失败", 500);
        }
    }
    protected static function testFunction($function, array $args, $result)
    {
        return static::testFunc($function, $args, $result);
    }
    protected static function testMethod($obj, string $method, array $args, $result)
    {
        return static::testFunc([$obj, $method], $args, $result);
    }
    protected static function testStaticMethod(string $class, string $method, array $args, $result)
    {
        return static::testFunc($class.'::'.$method, $args, $result);
    }
    protected static function testProperty($class, $property, $value)
    {
        if ($class->$property === $value) {
            base\response\Response::dump('属性:'.$class.'->$'.$property, '测试成功;');
        } else {
            base\response\Response::dump('属性:'.$class.'->$'.$property, '测试失败;');
        }
    }
    protected static function testStaticProperty($class, $property, $value)
    {
        if ($class::$property === $value) {
            base\response\Response::dump('属性:'.$class.'::$'.$property, '测试成功;');
        } else {
            base\response\Response::dump('属性:'.$class.'->$'.$property, '测试失败;');
        }
    }


    public function testAll(string $dir)
    {
        if (!is_dir($dir)) {
            throw new TestException('对应测试目录不存在');
        }

        $defined = get_declared_classes();

        foreach (base\dir\Dir::getAllFileByType($dir, 'Test.php') as $file) {
            require_once $file;
        }
        $nowDefined = get_declared_classes();

        $new = array_filter($nowDefined, function ($class) use ($defined) {
            return $class !== 'stdClass' && !array_search($class, $defined);
        });

        array_map(function($class) {
            $obj = new $class;
            if (is_subclass_of($obj, '\\msqphp\\test\\Test')) {
                $obj->init();
                $obj->testStart();
            }
            $obj = null;
        }, $new);
    }
    protected function getMethods($testObj) : array
    {
        return array_filter(get_class_methods($testObj), function($method) {
            return preg_match('/^test/', $method) !== 0 && !in_array($method, $this->not_test);
        });
    }
}