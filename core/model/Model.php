<?php declare(strict_types = 1);
namespace msqphp\core\model;

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
        $this->config = core\config\Config::get('model');
        core\database\Database::connect();
    }
    public function __destruct()
    {
        // core\database\Database::close();
    }
}