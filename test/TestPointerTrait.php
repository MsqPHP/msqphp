<?php declare(strict_types = 1);
namespace msqphp\test;

trait TestPointerTrait
{
    public $pointer = [];

    final public function init() : self
    {
        $this->pointer = [];

        return $this;
    }
    final public function clear() : self
    {
        if (isset($this->pointer['class'])) {
            $this->pointer= ['class'=>$this->pointer['class']];
        } elseif (isset($this->pointer['obj'])) {
            $this->pointer = ['obj'=>$this->pointer['obj']];
        } else {
            $this->pointer = [];
        }
        return $this;
    }
    final public function class(string $class) : self
    {
        $this->pointer['class'] = $class;
        return $this;
    }
    final public function obj($obj) : self
    {
        $this->pointer['obj'] = $obj;
        return $this;
    }
    final public function args() : self
    {
        $this->pointer['args'] = func_get_args();
        return $this;
    }
    final public function func($func) : self
    {
        $this->pointer['func'] = $func;
        return $this;
    }
    final public function method(string $method) : self
    {
        $this->pointer['method'] = $method;
        return $this;
    }
    final public function result($result) : self
    {
        $this->pointer['result'] = $result;
        return $this;
    }
    final public function test()
    {
        $pointer = $this->pointer;
        if (isset($pointer['class'])) {
            static::testStaticMethod($pointer['class'], $pointer['method'], $pointer['args'], $pointer['result']);
        } elseif (isset($pointer['obj'])) {
            $pointer['result'] === $this && $pointer['result'] = $pointer['obj'];
            static::testMethod($pointer['obj'], $pointer['method'], $pointer['args'], $pointer['result']);
        } elseif (isset($pointer['func'])) {
            static::testFunc($pointer['func'], $pointer['args'], $pointer['result']);
        } else {
            throw new TestException('不正确的测试方式');
        }
    }
}
