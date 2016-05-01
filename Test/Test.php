<?php declare(strict_types = 1);
namespace Core\Test;

class Test {
    protected $not_test = array('testStart','test','testClassFunc','testAll','testThis','testStaticMethod');

    public function testStart()
    {
        echo '测试文件正常运行，但未定义测试函数';
        echo '<hr/>';
        echo '应该且必须在该类中添加',__FUNCTION__,'函数，并调用相关方法';
        echo '<hr/>';
        echo '至少应该添加 $this->testThis()),测试该文件中所有test开头的函数';
        echo '<hr/>';
        echo '传参为null，返回值为true（错误有提示）';
        echo '<hr/>';
    }

    protected function test($func,$args=null,$result=null) : bool
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
        }
        $white_len =(int) (30 - strlen($func_str) < 0 ?: 0);
        $str = '函数：' . $func_str . str_repeat('&nbsp;',$white_len);

        $args = (array) $args;

        $func_result = call_user_func_array($func,$args);
        if (is_object($result) && $result($func_result)) {
            echo '<pre>';
            echo $str,'测试成功;<hr/>';
            echo '</pre>';
            return true;
        } elseif ($result === $func_result) {
            echo '<pre>';
            echo $str,'测试成功;<hr/>';
            echo '</pre>';
            return true;
        } else {
            echo '<pre style="color:red;">';
            echo $str,'测试失败;<hr/>';
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
            throw new \Exception("测试失败", 500);
            return false;
        }
    }
    protected function testClassFunc($class,$func,$args,$results) {
        if(is_array($args) && is_array($results) && count($args) === count($results)) {
            for($i=count($args);$i>0;--$i) {
                $this->test(array($class,$func),$args[$i],$results[$i]);
            }
        } else {
            $this->test(array($class,$func),$args,$results);
        }
    }
    protected function testThis() : bool
    {
        foreach ($this->getMethods($this) as $method) {
            $this->test(array($this,$method),null,true);
        }
        return true;
    }
    protected function getMethods($testObj) : array
    {
        return array_filter(get_class_methods($testObj),function($method){
            return preg_match('/^test/',$method) !== 0 && !in_array($method,$this->not_test);
        });
    }
    protected function testStaticMethod(string $method,array $args,$result) : bool
    {
        return $this->test($method,$args,$result);
    }
    public function testAll($dir)
    {
        $defined = get_declared_classes();
        foreach ($this->getAllTestFile($dir) as $file) {
            require_once $file;
        }
        $nowDefined = get_declared_classes();
        $new = array_filter($nowDefined,function($class) use($defined){
            return !array_search($class,$defined);
        });
        foreach ($new as $class) {
            if ($class === 'stdClass') {continue;}
            $obj = new $class;
            $obj->testStart();
            $obj = null;
        }
        return true;
    }
    protected function checkStaticProperty($class,$property,$value)
    {
        if ($class::$$property === $value) {
            echo '<pre>';
            echo '属性:'.$class.'::$'.$property,'测试成功;<hr/>';
            echo '</pre>';
        }
    }
    protected function setStaticProperty($class,$property,$value)
    {
        return $class::$$property = $value;
    }
    protected function getAllTestFile(string $path,string $ext = 'Test.php') : array {
        $path = realpath($path).DIRECTORY_SEPARATOR;
        //文件数组
        $file_arr = [];
        foreach(glob($path.'*'.$ext) as $file) {
            $file_arr[] = $file;
        };
        foreach ($this->getList($path,'dir') as $dir) {
            $file_arr = array_merge($file_arr,$this->getAllTestFile($path.$dir,$ext));
        }
        //返回
        return $file_arr;
    }
    /**
     * 得到当前目录列表
     * @param  string   $path 路径
     * @param  string   $type 类型(all|file|dir)
     * @return array            
     */
    protected function getList(string $dir,string $type = 'all') : array
    {
        //根据类型进一步过滤
        switch ($type) {
            case 'all':
                return array_filter(scandir($dir,0), function($path) {
                    return $path !== '.' && $path !== '..';
                },0);
                break;
            case 'file':
                return array_filter(scandir($dir,0), function($path) use ($dir) {
                    return is_file($dir.$path);
                },0);
                break;
            case 'dir':
                return array_filter(scandir($dir,0), function($path) use ($dir) {
                    return $path !== '.' && $path !== '..' && is_dir($dir.$path);
                },0);
                break;
            default:
                throw new FileException($type.'应为all|file|dir', 500);
                return [];
                break;
        }
    }
}