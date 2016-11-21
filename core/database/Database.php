<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;

final class Database
{
    use DatabaseHandlerTrait;
    use DatabaseOperateTrait;
    use DatabaseTransactionTrait;

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new DatabaseException($message);
    }
    public static function close() : void
    {
        static::$pdo = null;
    }
    // 获取数据库使用信息
    public static function getInfo() : array
    {
        return static::$info;
    }
}