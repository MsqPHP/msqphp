<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\core\traits;

class Container
{
    use traits\Instance;

    //服务列表
    private $bindings = [];
    //已经实例化的服务
    private $instances = [];

    //获取服务
    public function get(string $name, array $params = [])
    {
        //已存在,直接返回
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        //是否已绑定
        if (!isset($this->bindings[$name])) {
            // 对应文件是否存在
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'binds' . DIRECTORY_SEPARATOR . $name . '.php';
            // 判断用户扩展目录下是否存在
            is_file($file) || $file = \msqphp\Environment::getPath('library') . 'core' . DIRECTORY_SEPARATOR . 'container' . DIRECTORY_SEPARATOR . 'binds' . DIRECTORY_SEPARATOR . $name . '.php';
            // 存在载入
            if (is_file($file)) {
                $info = require $file;
                // 分享复制当实例集合中,否则直接返回
                $this->registerService($name, $info['object'], $info['shared']);
                // 重新获取
                return $this->get($name, $params);
            // 否则异常,取一个未知的容器值
            } else {
                throw new ContainerException($name . '并不存在于容器中');
            }
        }

        return $this->createObject($name, $params);
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

        // 闭包函数
        if ($concrete instanceof \Closure) {
            $object = call_user_func_array($concrete, $params);
        //匿名函数方式
        } elseif (is_object($concrete)) {
            $object = $concrete;
        //字符串方式
        } elseif (is_string($concrete)) {
            $object = empty($params) ? new $concrete : call_user_func_array([new \ReflectionClass($concrete),'newInstanceArgs'], $params);
        } else {
            $object = null;
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
    public function remove(string $name) : void
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
        $this->bindings[$name] = ['class' => $class, 'shared' => $shared];
    }

    // 对象接口
    public function __get(string $name)
    {
        return $this->get($name);
    }
    public function __set(string $name, $value)
    {
        return $this->setShared($name, $value);
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