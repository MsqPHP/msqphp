<?php declare(strict_types = 1);
namespace msqphp\main\cache;

trait CacheOperateTrait
{
    // 当前处理缓存键是否存在
    public function exists() : bool
    {
        return $this->getHander()->available($this->getKey());
    }
    public function available() : bool
    {
        return $this->exists();
    }

    // 得到当前处理缓存键对应值
    public function get()
    {
        try {
            return $this->getHander()->get($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            static::exception($this->getKey().'缓存无法获取,原因:'.$e->getMessage());
        }
    }

    // 自增
    public function inc() : int
    {
        return $this->increment();
    }
    public function increment() : int
    {
        try {
            return $this->getHander()->increment($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            static::exception($this->getKey().'缓存无法自增,原因:'.$e->getMessage());
        }
    }

    // 自减
    public function dec() : int
    {
        return $this->decrement();
    }
    public function decrement() : int
    {
        try {
            return $this->getHander()->decrement($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            static::exception($this->getKey().'缓存无法自减,原因:'.$e->getMessage());
        }
    }

    // 设置当前处理缓存键 对应值
    public function set() : void
    {
        try {
            $this->getHander()->set($this->getKey(), $this->getValue(), $this->getExpire());
        } catch(handlers\CacheHandlerException $e) {
            static::exception($this->getKey().'缓存无法赋值,原因:'.$e->getMessage());
        }
    }

    // 删除当前处理缓存键
    public function delete() : void
    {
        try {
            $this->getHander()->delete($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            static::exception($this->getKey().'缓存无法删除,原因:'.$e->getMessage());
        }
    }

    // 清楚所有过期缓存
    public function clear() : void
    {
        try {
            $this->getHander()->clear();
        } catch(handlers\CacheHandlerException $e) {
            static::exception('缓存无法清空,原因:'.$e->getMessage());
        }
    }
    public function flush() : void
    {
        $this->clear();
    }

    // 得到缓存真实键
    private function getKey() : string
    {
        // 不存在异常
        isset($this->pointer['key']) || static::exception('未选择任意缓存键');
        // 添加前缀
        return ($this->pointer['prefix'] ?? static::$config['prefix']) . $this->pointer['key'];
    }
    // 得到缓存值
    private function getValue()
    {
        isset($this->pointer['value']) || static::exception('未给当前缓存设置任意赋值');

        return $this->pointer['value'];
    }
    // 得到过期时间
    private function getExpire() : int
    {
        return HAS_CACHE ? $this->pointer['expire'] ?? static::$config['expire'] : 0;
    }


    // 得到缓存处理器
    private function getHander() : handlers\CacheHandlerInterface
    {
        // 如果处理器存在,取其,直接返回
        if (isset($this->pointer['handler'])) {
            return $this->pointer['handler'];
        // 有种类,则获取对应处理器
        } elseif (isset($this->pointer['type'])) {
            return $this->pointer['handler'] = static::getCacheHandler($this->pointer['type'], $this->pointer['config'] ?? []);
        // 取默认
        } else {
            return $this->pointer['handler'] = static::getDefaultHandler();
        }
    }
}