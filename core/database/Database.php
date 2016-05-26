<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Database
{
    private static $config = [];
    private static $pdo = null;
    private static $sqls = [];

    public static function connect()
    {
        if (null === static::$pdo) {
            static::$config = core\config\Config::get('database');
            try {
                $connect_info = static::getConnectInfo();
                static::$pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $params);
                static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
            static::$pdo->exec('SET NAMES '.$config['charset']);
        }
    }
    private static function getConnectInfo() : array
    {
        $config = static::$config;
        switch ($config['type']) {
                case 'mysql':
                    $dsn = $config['type'].':host='.$config['host'].';port='.$config['port'].';dbname='.$config['name'].';charset='.$config['charset'].';';
                    return ['dsn'=>$dsn,'username'=>$config['username'],'password'=>$config['password']];
                case 'pgsql':
                    $dsn = $config['type'].':host='.$config['host'].' port='.$config['port'].' dbname='.$config['name'].' user='.$config['username'].' password='.$config['password'];
                    return ['dsn'=>$dsn,'username'=>'',$password=>''];
                case 'sqllite':
                    $dsn = 'sqlite:' . $config['name'];
                    return ['dsn'=>$dsn,'username'=>'',$password=>''];
                case 'oci':
                case 'oracle':
                    $dsn = 'oci:dbname=' . $config['database'].';charset='.$config['charset'];
                    return ['dsn'=>$dsn,'username'=>$config['username'],'password'=>$config['password']];
                default:
                    throw new DatabaseException('未知的数据库类型', 1);
        }
    }
    public static function get(string $sql, array $prepare)
    {
        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetchAll(2);
            } else {
                $stat = static::$pdo->prepare($sql);
                static::bindParam($stat, $prepare);
                $stat->execute();
                $result = $stat->fetchAll(2);
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getOne(string $sql, array $prepare)
    {
        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetch(2);
            } else {
                $stat = static::$pdo->prepare($sql);
                static::bindParam($stat, $prepare);
                $stat->execute();
                return $stat->fetch(2);
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getColumn(string $sql, $prepare)
    {
        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetchColumn();
            } else {
                $stat = static::$pdo->prepare($sql);
                static::bindParam($stat, $prepare);
                $stat->execute();
                return $stat->fetchColumn();
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function query(string $sql, array $prepare)
    {
        static::$sqls[] = $sql;
        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql);
            } else {
                $stat = static::$pdo->prepare($sql);
                static::bindParam($stat, $prepare);
                $stat->execute();
                return $stat->fetchAll(2);
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function exec(string $sql,  array $prepare) : int
    {
        static::$sqls[] = $sql;
        try {
            if (empty($prepare)) {
                return static::$pdo->exec($sql);
            } else {
                $stat = static::$pdo->prepare($sql);
                static::bindParam($stat, $prepare);
                $stat->execute();
                return $stat->rowCount();
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    private static function bindParam($stat, array $prepare)
    {
        try {
            foreach ($prepare as $key => $value) {
                $stat->bindParam($key,$value[0],$value[1]);
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
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
    public static function lastInsertId() : int
    {
        try {
            return (int) static::$pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
}