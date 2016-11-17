<?php declare(strict_types = 1);
namespace msqphp\core\database;

trait DatabaseOperateTrait
{
    public static function get(string $sql, array $prepare = [])
    {
        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::prepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getOne(string $sql, array $prepare = [])
    {

        try {
            if (empty($prepare)) {
                $result = static::sqlQuery($sql)->fetch(\PDO::FETCH_ASSOC);
            } else {
                $result = static::prepare($sql, $prepare)->fetch(\PDO::FETCH_ASSOC);
            }
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getColumn(string $sql, $prepare)
    {
        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchColumn() : static::prepare($sql, $prepare)->fetchColumn();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function query(string $sql, array $prepare = [])
    {

        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::prepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function exec(string $sql,  array $prepare = []) : ?int
    {
        try {
            $result = empty($prepare) ? static::sqlExec($sql) : static::prepare($sql, $prepare)->rowCount();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
}