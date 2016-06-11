<?php declare(strict_types = 1);
namespace msqphp\core\cache;

trait CacheOperateTrait
{
    /**
     * 当前处理缓存键是否存在
     *
     * @return bool
     */
    public function exists() : bool
    {
        return !defined('NO_CACHE') && $this->pointer['handler']->available($this->getKey());
    }
    public function available() : bool
    {
        return $this->exists();
    }
    /**
     * 得到当前处理缓存键对应值
     *
     * @throws CacheException
     * @return miexd
     */
    public function get()
    {
        try {
            return $this->pointer['handler']->get($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($this->getKey().'缓存无法获取,原因:'.$e->getMessage());
        }
    }
    /**
     * 自增
     *
     * @throws CacheException
     *
     * @return int
     */
    public function inc() : int
    {
        return $this->increment();
    }
    public function increment() : int
    {
        try {
            return $this->pointer['handler']->increment($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($this->getKey().'缓存无法自增,原因:'.$e->getMessage());
        }
    }
    /**
     * 自减
     *
     * @throws CacheException
     * @return int
     */
    public function dec()
    {
        return $this->decrement();
    }
    public function decrement()
    {
        try {
            return $this->pointer['handler']->decrement($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($this->getKey().'缓存无法自减,原因:'.$e->getMessage());
        }
    }
    /**
     * 设置当前处理缓存键 对应值
     *
     * @throws CacheException
     * @return self
     */
    public function set() : self
    {
        if (!defined('NO_CACHE')) {
            try {
                $this->pointer['handler']->set($this->getKey(), $this->pointer['value'], $this->pointer['expire'] ?? $this->config['expire']);
            } catch(handlers\CacheHandlerException $e) {
                throw new CacheException($this->getKey().'缓存无法赋值,原因:'.$e->getMessage());
            }
        }
        return $this;
    }
    /**
     * 删除当前处理缓存键
     *
     * @throws CacheException
     * @return self
     */
    public function delete() : self
    {
        try {
            $this->pointer['handler']->delete($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($this->getKey().'缓存无法删除,原因:'.$e->getMessage());
        }
        return $this;
    }
    /**
     * 清楚所有过期缓存
     *
     * @throws CacheException
     * @return self
     */
    public function clear() : self
    {
        try {
            $this->pointer['handler']->clear();
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException('缓存无法清空,原因:'.$e->getMessage());
        }
        return $this;
    }

    /**
     * 得到缓存真实键
     * @return string
     */
    private function getKey() : string
    {
        if (isset($this->pointer['key'])) {
            return ($this->pointer['prefix'] ?? $this->config['prefix']) . $this->pointer['key'];
        } else {
            throw new CacheException('未选择任意缓存键');
        }
    }
}