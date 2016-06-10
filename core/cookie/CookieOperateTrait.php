<?php declare(strict_types = 1);
namespace msqphp\core\cookie;

use msqphp\base;
use msqphp\core;

trait CookieOperateTrait
{
    /**
     * cookie是否存在
     * @return bool
     */
    public function exists() : bool
    {
        return isset($this->cookies[$this->getKey()]);
    }
    /**
     * 得到当前操作cookie值 或者 得到全部cookie值
     * @return string|array
     */
    public function get()
    {
        return isset($this->pointer['key']) ? $this->getVal('decode') : $this->cookies;
    }
    /**
     * 设置cookie值
     * @throws CookieException
     * @return self
     */
    public function set() : self
    {
        //获得cookie信息
        $pointer   = $this->pointer;

        $key      = $this->getKey();
        $value    = (string) $this->getVal('encode');

        $expire   = time() + ( $pointer['expire'] ?? $this->config['expire'] );
        $path     = $pointer['path']     ?? $this->config['path'];
        $domain   = $pointer['domain']   ?? $this->config['domain'];
        $secure   = $pointer['secure']   ?? $this->config['secure'];
        $httponly = $pointer['httponly'] ?? $this->config['httponly'];

        $func     = ( $pointer['transcoding'] ?? $this->config['transcoding'] ) ? 'setcookie' : 'setrawcookie';

        if (!$func($key, $value, $expire, $path, $domain, $secure, $httponly)) {
            throw new CookieException('未知错误, 无法定义cookie');
        }
        $this->cookies[$key] = $value;
        return $this;
    }
    /**
     * 删除cookie
     * @throws CookieException
     * @return self
     */
    public function delete() : self
    {
        setcookie($this->getKey(), '', 0);
        return $this;
    }
    /**
     * 清除指定前缀cookie
     * @throws CookieException
     * @return self
     */
    public function clear() : self
    {
        //遍历
        foreach ($this->cookies as $key => $value) {
            setcookie($key, '', 0);
        }
        $this->cookies = [];
        return $this;
    }


    /**
     * 得到当前操作cookie正确键值
     * @param  string $key
     * @return string
     */
    private function getKey() : string
    {
        if (isset($this->pointer['key'])) {
            return ($this->pointer['prefix'] ?? $this->config['prefix']).$this->pointer['key'];
        } else {
            throw new CookieException('未选定任意cookie');
        }
    }
    private function getVal($type = '')
    {
        $pointer = $this->pointer;

        if (isset($pointer['value'])) {
            $value = $pointer['value'];
        } else {
            $key = $this->getKey();
            if (isset($this->cookies[$key])) {
                $value = $this->cookies[$key];
            } else {
                throw new CookieException('cookie值不存在');
            }
        }
        if ('encode' === $type && isset($pointer['encode']) && $pointer['encode'] || $this->config['encode']) {
            $value = $this->encodeValue($value);
        }
        if ('decode' === $type && isset($pointer['decode']) && $pointer['decode']  || $this->config['encode']) {
            $value = $this->decodeValue($value);
        }
        return $value;
    }
    /**
     * 加密当前cookie值
     * @return void
     */
    private function encodeValue($value) : string
    {
        return base\crypt\Crypt::encrypt(serialize($value));
    }
    /**
     * 解密当前cookie值
     * @return void
     */
    private function decodeValue(string $value)
    {
        return base\crypt\Crypt::decrypt(unserialize($value));
    }
}