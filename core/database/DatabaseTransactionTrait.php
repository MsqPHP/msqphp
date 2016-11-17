<?php declare(strict_types = 1);
namespace msqphp\core\database;

trait DatabaseTransactionTrait
{
    // 开始事物
    public static function beginTransaction() : void
    {
        static::$pdo->beginTransaction();
    }

    // 提交事物
    public static function commit() : void
    {
        static::$pdo->commit();
    }

    // 回滚事物
    public static function rollBack() : void
    {
        static::$pdo->rollBack();
    }
}