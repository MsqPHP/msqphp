<?php declare(strict_types = 1);
namespace msqphp\test\base\number;

class NumberTest extends \msqphp\test\Test
{
    public function testStart()
    {
        $this->init();
        $this->class('\msqphp\base\number\Number');
        $this->testThis();
    }
    public function testGetSize()
    {
        $this->clear();
        $this->method('byte');
        $this->args(0, true)->result('0 Bytes')->test();
        $this->args(0, true)->result('0 Bytes')->test();
        $this->args(1024, true)->result('1 KB')->test();
        $this->args(0.2, false)->result('0.2 Bytes')->test();
    }
}