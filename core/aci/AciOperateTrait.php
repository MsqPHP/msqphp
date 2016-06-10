<?php declare(strict_types = 1);
namespace msqphp\core\aci;

trait AciOperateTrait
{
    public function get()
    {
        if ('role' === $this->pointer['type']) {
            return $this->roles;
        } elseif ('resource' === $this->pointer['type']) {
            return $this->resources;
        } else {
            throw new AciException('未设置获取类型');
        }
    }
    public function create()
    {
    }
    public function new()
    {
    }
    public function allow()
    {

    }
    public function deny()
    {
    }
    public function isAllowed() : bool
    {

    }
    public function isDenied() : bool
    {
    }
}