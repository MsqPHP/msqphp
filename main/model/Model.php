<?php declare(strict_types = 1);
namespace msqphp\main\model;

use msqphp\core;

class Model
{
    use ModelStaticTrait;
    // 指针层
    use ModelPointerTrait;
    // 操作层
    use ModelOperateTrait;

    public function __construct()
    {
        static::initStatic();
    }
    protected static function exception(string $message) : void
    {
        throw new ModelException($message);
    }
}