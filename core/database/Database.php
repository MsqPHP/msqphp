<?php declare(strict_types = 1);
namespace msqphp\core\database;

use msqphp\base;

final class Database
{
    use DatabaseTransactionTrait;
    use DatabaseOperateTrait;

    private static $config = [];
    private static $pdo = null;
    private static $info = [];

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new DatabaseException($message);
    }
    // 连接
    public static function connect()
    {
        if (null === static::$pdo) {
            static::$config = $config = app()->config->get('database');
            try {
                $connect_info = static::getConnectInfo();
                static::$pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $config['params']);
                static::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                static::exception($e->getMessage());
            }
            static::exec('SET NAMES ' . $config['charset']);
        }
    }
    // 获取连接信息
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
                static::exception('未知的数据库类型');
        }
    }

    // 执行sql语句
    private static function sqlQuery(string $sql) : \PDOStatement
    {
        if (false === $stat = static::$pdo->query($sql)) {
            static::exception('错误的query语句:'.$sql);
        } else {
            return $stat;
        }
    }
    // 执行exec语句
    private static function sqlExec(string $sql) : int
    {
        try {
            return static::$pdo->exec($sql);
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
    }
    // 执行预处理语句
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
            static::exception($e->getMessage());
        }
    }
    // 获取最后插入id
    public static function lastInsertId() : int
    {
        try {
            return (int) static::$pdo->lastInsertId();
        } catch (\PDOException $e) {
            static::exception($e->getMessage());
        }
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