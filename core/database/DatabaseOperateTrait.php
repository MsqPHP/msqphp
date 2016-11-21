<?php declare(strict_types = 1);
namespace msqphp\core\database;

trait DatabaseOperateTrait
{
    public static function get(string $sql, array $prepare = [])
    {
        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::sqlPrepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getOne(string $sql, array $prepare = [])
    {

        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetch(\PDO::FETCH_ASSOC) : static::sqlPrepare($sql, $prepare)->fetch(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function getColumn(string $sql, array $prepare = [])
    {
        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchColumn() : static::sqlPrepare($sql, $prepare)->fetchColumn();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function query(string $sql, array $prepare = [])
    {

        try {
            $result = empty($prepare) ? static::sqlQuery($sql)->fetchAll(\PDO::FETCH_ASSOC) : static::sqlPrepare($sql, $prepare)->fetchAll(\PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    public static function exec(string $sql,  array $prepare = []) : ?int
    {
        try {
            $result = empty($prepare) ? static::sqlExec($sql) : static::sqlPrepare($sql, $prepare)->rowCount();
            return $result === false ? null : $result;
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    // 获取最后插入id
    public static function lastInsertId() : int
    {
        try {
            return (int) static::getHandler()->lastInsertId();
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    // 执行sql语句
    private static function sqlQuery(string $sql) : \PDOStatement
    {
        return static::getHandler()->query($sql);
    }
    // 执行exec语句
    private static function sqlExec(string $sql) : int
    {
        return static::getHandler()->exec($sql);
    }
    // 执行预处理语句
    private static function sqlPrepare(string $sql, array $prepare = []) : \PDOStatement
    {
        $stat = static::getHandler()->prepare($sql);
        foreach ($prepare as $key => $value) {
            // 引用传递,避免出错
            $stat->bindParam($key, $prepare[$key][0], $prepare[$key][1]);
        }
        $stat->execute();
        return $stat;
    }
}