<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;
use msqphp\core;

final class Database
{
    use DatabaseTransactionTrait;
    use DatabaseOperateTrait;

    private static $config = [];
    private static $pdo = null;
    private static $sqls = [];
    private static $times = [];

    public static function connect()
    {
        if (null === static::$pdo) {
            static::$config = $config = core\config\Config::get('database');
            try {
                $connect_info = static::getConnectInfo();
                $start = microtime(true);
                static::$pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $config['params']);
                static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $end = microtime(true);
                static::$times = ['init' => $end-$start, 'total'=>$end-$start];
            } catch (\PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
            static::exec('SET NAMES '.$config['charset']);
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
                throw new DatabaseException('未知的数据库类型');
        }
    }
    private static function sqlQuery(string $sql)
    {
        if (false === $stat = static::$pdo->query($sql)) {
            throw new DatabaseException('错误的query语句:'.$sql);
        } else {
            static::$sqls[] = $sql;
            return $stat;
        }
    }
    private static function sqlExec(string $sql) : int
    {
        static::$sqls[] = $sql;
        return static::$pdo->exec($sql);
    }
    private static function prepare(string $sql, array $prepare = []) : \PDOStatement
    {
        static::$sqls[] = $sql;
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
    public static function getTimes()
    {
        return static::$times;
    }
}