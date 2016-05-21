<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;
use msqphp\core;

class Database
{
    private static $pdo = null;
    private static $instance = null;
    private static $sqls = [];
    private function __construct()
    {
        if (null === static::$pdo) {
            $config = core\config\Config::get('database');
            switch ($config['type']) {
                case 'mysql':
                    $dns = $config['type'].':host='.$config['host'].';port='.$config['port'].';dbname='.$config['name'].';charset='.$config['charset'].';';
                    break;
                default:
                    throw new DatabaseException('未知的数据库类型', 1);
            }
            try {
                static::$pdo = new \PDO($dns, $config['username'], $config['password'], $config['params']);
                static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
            static::$pdo->exec('SET NAMES '.$config['charset']);
        }
    }
    /**
     * 得到database实例
     * @return self
     */
    public static function getInstance() : self
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
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