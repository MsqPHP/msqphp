<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;

final class Database
{

    private static $config = [];

    use DatabaseHandlerTrait;
    use DatabaseOperateTrait;
    use DatabaseTransactionTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new DatabaseException($message);
    }

    // 获取数据库使用信息
    public static function getInfo() : array
    {
        return static::$info;
    }

    private static function getConfig(?string $name = null)
    {
        // 如果当前配置为空
        if ([] === static::$config) {
            // 获取对应配置
            static::$config = $config = app()->config->get('database');
        }
        if (null === $name) {
            return static::$config;
        } else {
            isset(static::$config[$name]) || static::exception('数据库'.$name.'配置不存在');
            return static::$config[$name];
        }
    }
}