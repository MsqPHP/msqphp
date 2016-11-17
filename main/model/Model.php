<?php declare(strict_types = 1);
namespace msqphp\main\model;

use msqphp\core;

abstract class Model
{
    private $config = [];
    // 指针层
    use ModelPointerTrait;
    // sql拼接层
    use ModelSqlTrait;
    // 操作层
    use ModelOperateTrait;

    public function __construct()
    {
        $this->config = app()->config->get('model');
        core\database\Database::connect();
    }
    public function __destruct()
    {
         core\database\Database::close();
    }
}