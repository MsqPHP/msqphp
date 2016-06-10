<?php declare(strict_types = 1);
namespace msqphp\core\model;

use msqphp\base;
use msqphp\core;


abstract class Model
{
    //指针层
    use ModelPointerTrait;
    //sql拼接层
    use ModelSqlTrait;
    //操作层
    use ModelOperateTrait;

    public function __construct()
    {
        core\database\Database::connect();
    }
}