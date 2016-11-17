<?php declare(strict_types = 1);
namespace msqphp\main\view;

use msqphp\core;

trait ViewGroupTrait
{
    private $group = '';
    private function initGroupInfo() : void
    {
        // 获取分组信息
        $group_info = core\route\Route::getGroupInfo();
        $group = '';

        for ($i = 0, $l = count($group_info) / 2; $i < $l; ++$i) {
            $group .=  $group_info[$i] . DIRECTORY_SEPARATOR;
        }

        $this->group = $group;
    }

    public function getGroup() : string
    {
        if ($this->group === '') {
            $this->initGroupInfo();
        }
        return $this->group;
    }
    public function setGoup(string $group) : void
    {
        $this->group = $group;
    }
    public function group(?string $group = null)
    {
        if (null === $group) {
            return $this->getGroup();
        } else {
            $this->setGoup($group);
            return $this;
        }
    }
}