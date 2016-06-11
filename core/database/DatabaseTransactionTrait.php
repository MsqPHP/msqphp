<?php declare(strict_types = 1);
namespace msqphp\core\database;

trait DatabaseTransactionTrait
{
    public static function beginTransaction()
    {
        static::$pdo->beginTransaction();
    }
    public static function commit()
    {
        static::$pdo->commit();
    }
    public static function rollBack()
    {
        static::$pdo->rollBack();
    }
}