<?php declare(strict_types = 1);
namespace msqphp\core\database;

trait DatabaseOperateTrait
{
    public static function get(string $sql, array $prepare = [])
    {
        try {
            $start = microtime(true);

            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::prepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);

            static::$times['sqls'][] = ['sql'=>$sql,'time'=>microtime(true)-$start];
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getOne(string $sql, array $prepare = [])
    {

        try {
            $start = microtime(true);
            if (empty($prepare)) {
                $result = static::sqlQuery($sql)->fetch(\PDO::FETCH_ASSOC);
            } else {
                $result = static::prepare($sql, $prepare)->fetch(\PDO::FETCH_ASSOC);
            }
            static::$times['sqls'][] = ['sql'=>$sql,'time'=>microtime(true)-$start];
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getColumn(string $sql, $prepare)
    {
        try {
            $start = microtime(true);
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchColumn() : static::prepare($sql, $prepare)->fetchColumn();
            static::$times['sqls'][] = ['sql'=>$sql,'time'=>microtime(true)-$start];
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function query(string $sql, array $prepare = [])
    {

        try {
            $start = microtime(true);
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::prepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            static::$times['sqls'][] = ['sql'=>$sql,'time'=>microtime(true)-$start];
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function exec(string $sql,  array $prepare = []) : int
    {
        try {
            $start = microtime(true);
            $result = empty($prepare) ? static::sqlExec($sql) : static::prepare($sql, $prepare)->rowCount();
            static::$times['sqls'][] = ['sql'=>$sql,'time'=>microtime(true)-$start];
            return $result === false ? 0 : $result;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}