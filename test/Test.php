<?php declare(strict_types = 1);
namespace msqphp\test;

use msqphp\base;

class Test {
    protected $not_test = ['test', 'testProperty', 'testStaticProperty', 'testStart', 'testFunc', 'testMethod', 'testAll', 'testThis', 'testStaticMethod', 'testFunction'];

    public $test = [];
    public function init() : self
    {
        $this->test = [];
        return $this;
    }
    public function clear() : self
    {
        if (isset($this->test['class'])) {
            $this->test = ['class'=>$this->test['class']];
        } elseif (isset($this->test['obj'])) {
            $this->test = ['obj'=>$this->test['obj']];
        } else {
            $this->test = [];
        }
        return $this;
    }
    public function class(string $class) : self
    {
        $this->test['class'] = $class;
        return $this;
    }
    public function obj($obj) : self
    {
        $this->test['obj'] = $obj;
        return $this;
    }
    public function args() : self
    {
        $this->test['args'] = func_get_args();
        return $this;
    }
    public function func($func) : self
    {
        $this->test['func'] = $func;
        return $this;
    }
    public function method(string $method) : self
    {
        $this->test['method'] = $method;
        return $this;
    }
    public function result($result) : self
    {
        $this->test['result'] = $result;
        return $this;
    }
    public function test()
    {
        $test = $this->test;
        if (isset($test['class'])) {
            static::testStaticMethod($test['class'], $test['method'], $test['args'], $test['result']);
        } elseif (isset($test['obj'])) {
            $test['result'] === $this && $test['result'] = $test['obj'];
            static::testMethod($test['obj'], $test['method'], $test['args'], $test['result']);
        } elseif (isset($test['func'])) {
            static::testFunc($test['func'], $test['args'], $test['result']);
        } else {
            throw new TestException('不正确的测试方式');
        }
    }



    public function testStart()
    {
        echo '测试文件正常运行，但未定义测试函数';
        echo '<hr/>';
        echo '应该且必须在该类中添加', __FUNCTION__, '函数，并调用相关方法';
        echo '<hr/>';
        echo '至少应该添加 $this->testThis()), 测试该文件中所有test开头的函数';
        echo '<hr/>';
        echo '传参为null，返回值为true（错误有提示）';
        echo '<hr/>';
    }

    protected function testThis()
    {
        foreach ($this->getMethods($this) as $method) {
            static::testFunc([$this, $method], null, null);
        }
    }
    protected static function testFunc($func, $args=null, $result=null)
    {

        if(is_object($func[0])) {
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
        } catch (\Exception $e) {
            $func_result = $e->getMessage();
        }

        if ($result === $func_result || is_object($result) && $result($func_result)) {
            echo '<pre>';
            echo $str, '测试成功;<hr/>';
            echo '</pre>';
        } else {
            echo '<pre style="color:red;">';
            echo $str, '测试失败;<hr/>';
            echo '参数：';
            var_dump($args);
            echo '<hr/>';
            echo '结果应为：';
            var_dump($result);
            echo '<hr/>';
            echo '实际结果：';
            var_dump($func_result);
            echo '<hr/>';
            echo '</pre>';
            throw new TestException("测试失败", 500);
        }
    }
    protected static function testFunction($function, $args, $result)
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
            echo '<pre>';
            echo '属性:'.$class.'->$'.$property, '测试成功;<hr/>';
            echo '</pre>';
        }
    }
    protected static function testStaticProperty($class, $property, $value)
    {
        if ($class::$property === $value) {
            echo '<pre>';
            echo '属性:'.$class.'::$'.$property, '测试成功;<hr/>';
            echo '</pre>';
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

        $new = array_filter($nowDefined, function($class) use($defined){
            return $class !== 'stdClass' && !array_search($class, $defined);
        });

        foreach ($new as $class) {
            $obj = new $class;
            if (is_subclass_of($obj, '\\msqphp\\test\\Test')) {
                $obj->testStart();
            }
            $obj = null;
        }
    }
    
    protected function getMethods($testObj) : array
    {
        return array_filter(get_class_methods($testObj), function($method){
            return preg_match('/^test/', $method) !== 0 && !in_array($method, $this->not_test);
        });
    }
}