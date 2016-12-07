<?php declare(strict_types = 1);
namespace msqphp\core\database;


trait DatabaseHandlerTrait
{
    private static $config = [];
    private static $handler = null;
    private static $handlers = [];

    // 获取连接信息
    private static function getConnectInfo(array $config) : array
    {
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

    public static function getHandler(?string $name = null)
    {
        if (null === $name) {
            return static::$handler = static::$handler ?? static::initHandler();
        }

        return static::$handlers[$name] = static::$handlers[$name] ?? static::initHandler(null);;
    }

    public static function setHandler(string $name)
    {
        static::$handler = static::getHandler($name);
    }

    private static function initHandler(?string $name = null)
    {
        try {
            $config = static::getConfig($name);
            $connect_info = static::getConnectInfo($config);
            $pdo = new \PDO($connect_info['dsn'], $connect_info['username'], $connect_info['password'], $config['params']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec('SET NAMES ' . $config['charset']);
            return $pdo;
        } catch (\PDOException $e) {
            static::exception($name.'数据库初始化失败,原因:'.$e->getMessage());
        }
    }

    private static function getConfig(?string $name = null)
    {
        if ([] === static::$config) {
            static::$config = $config = app()->config->get('database');
        }
        if (null === $name) {
            return static::$config;
        } else {
            isset(static::$config[$name]) || static::exception('数据库'.$name.'配置不存在');
            return static::$config[$name];
        }
    }
}