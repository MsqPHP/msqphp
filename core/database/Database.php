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
            static::$config = $config = core\config\Config::get('database');
            try {
                $connect_info = static::getConnectInfo();
                static::$pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $config['params']);
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
    public static function get(string $sql, array $prepare = [])
    {
        static::$sqls[] = $sql;

        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $stat = static::prepare($sql, $prepare);
                $result = $stat->fetchAll(\PDO::FETCH_ASSOC);
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getOne(string $sql, array $prepare = [])
    {
        static::$sqls[] = $sql;

        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);
            } else {
                $stat = static::prepare($sql, $prepare);
                $result = $stat->fetch(\PDO::FETCH_ASSOC);
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    public static function getColumn(string $sql, $prepare)
    {
        static::$sqls[] = $sql;
        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetchColumn();
            } else {
                $stat = static::prepare($sql, $prepare);
                $result = $stat->fetchColumn();
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function query(string $sql, array $prepare = [])
    {
        static::$sqls[] = $sql;

        try {
            if (empty($prepare)) {
                return static::$pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $stat = static::prepare($sql, $prepare);
                $result = $stat->fetchAll(\PDO::FETCH_ASSOC);
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }

    public static function exec(string $sql,  array $prepare = []) : int
    {
        static::$sqls[] = $sql;

        try {
            if (empty($prepare)) {
                return static::$pdo->exec($sql);
            } else {
                $stat = static::prepare($sql, $prepare);
                $result = $stat->rowCount();
                unset($stat);
                return $result;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
    }
    private static function prepare(string $sql, array $prepare = []) : \PDOStatement
    {
        try {
            $stat = static::$pdo->prepare($sql);
            foreach ($prepare as $key => $value) {
                $stat->bindParam($key,$value[0],$value[1]);
            }
            $stat->execute();
            return $stat;
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

    public static function getSqls()
    {
        return static::$sqls;
    }
}