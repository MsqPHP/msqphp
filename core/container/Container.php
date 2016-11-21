<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\core\traits;

class Container implements \ArrayAccess
{
    use traits\Instance;

    //服务列表
    private $bindings = [];
    //已经实例化的服务
    private $instances = [];

    //获取服务
    public function get(string $name, array $params = [])
    {
        //先从已经实例化的列表中查找
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        //检测有没有注册该服务
        if (!isset($this->bindings[$name])) {
            // 文件是否存在
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'binds' . DIRECTORY_SEPARATOR . $name . '.php';
            // 判断用户扩展目录下是否存在
            is_file($file) || $file = \msqphp\Environment::getPath('library') . 'core' . DIRECTORY_SEPARATOR . 'container' . DIRECTORY_SEPARATOR . 'binds' . DIRECTORY_SEPARATOR . $name . '.php';
            // 存在载入
            if (is_file($file)) {
                $info = require $file;
                // 分享复制当实例集合中,否则直接返回
                return $info['shared'] === true ? $this->instances[$name] = call_user_func_array($info['object'], []) : call_user_func_array($info['object'], []);
            // 否则取null
            } else {
                throw new ContainerException($name . '并不存在于容器中');
            }
        } else {
            return $this->createObj($name, $params);
        }
    }
    /**
     * 创建对象
     *
     * @param   string  $name    名称
     * @param   array   $params  参数
     *
     * @return  object
     */
    private function createObject(string $name, array $params = [])
    {
        $concrete = $this->bindings[$name]['class'];//对象具体注册内容

        $object = null;

        //匿名函数方式
        if ($concrete instanceof \Closure) {
            $object = call_user_func_array($concrete,$params);
        //字符串方式
        } elseif (is_string($concrete)) {
            if (empty($params)) {
                $object = new $concrete;
            } else {
                //带参数的类实例化，使用反射
                $class = new \ReflectionClass($concrete);
                $object = $class->newInstanceArgs($params);
            }
        }

        //如果是共享服务，则写入_instances列表，下次直接取回
        $this->bindings[$name]['shared'] === true && $this->instances[$name] = $object;

        return $object;
    }

    //检测是否已经绑定
    public function has(string $name) : bool
    {
        return isset($this->bindings[$name]) || isset($this->instances[$name]);
    }

    //卸载服务
    public function remove(string $name) : bool
    {
        unset($this->bindings[$name],$this->instances[$name]);
    }

    //设置服务
    public function set(string $name, $class) : void
    {
        $this->registerService($name, $class, false);
    }

    //设置共享服务
    public function setShared(string $name, $class) : void
    {
        $this->registerService($name, $class, true);
    }

    //注册服务
    private function registerService(string $name, $class, bool $shared = false): void
    {
        $this->remove($name);
        if (!($class instanceof \Closure) && is_object($class)) {
            $this->instances[$name] = $class;
        } else {
            $this->bindings[$name] = ['class' => $class, 'shared' => $shared];
        }
    }
    //ArrayAccess接口

    //检测服务是否存在
    public function offsetExists($name)
    {
        return $this->has($name);
    }
    //以$di[$name]方式获取服务
    public function offsetGet($name)
    {
        return $this->get($name);
    }
    //以$di[$name]=$value方式注册服务，非共享
    public function offsetSet($name, $value)
    {
        return $this->set($name,$value);
    }
    //以unset($di[$name])方式卸载服务
    public function offsetUnset($name)
    {
        return $this->remove($name);
    }

    // 对象接口
    public function __get(string $name)
    {
        return $this->get($name);
    }
    public function __set(string $name, $value)
    {
        return $this->setShared($name);
    }
    public function __unset(string $name)
    {
        return $this->remove($name);
    }
    public function __isset(string $name)
    {
        return $this->has($name);
    }
}