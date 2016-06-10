<?php declare(strict_types = 1);
namespace msqphp\core\aci;

trait AciPointerTrait
{
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function name(string $name) : self
    {
        $this->pointer['name'] = $name;
        return $this;
    }
    public function data(array $data) : self
    {
        $this->pointer['data'] = $data;
        return $this;
    }
    public function role() : self
    {
        $this->pointer['type'] = 'role';
        return $this;
    }
    public function resources() : self
    {
        $this->pointer['type'] = 'resources';
        return $this;
    }
}