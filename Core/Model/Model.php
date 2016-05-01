<?php declare(strict_types = 1);
namespace Msqphp\Base\Model;

class Model
{
    // 当前pdo对象
    static protected $pdo  = null;
    protected $stat = null;
    protected $table = '';
    protected $primary_key = '';
    //所有执行过的sql
    public $sqls = [];
    //当前操作sql
    protected $sql = [];
    /**
     * 构造方法
     */
    public function __construct()
    {
        if (null === static::$pdo) {
            $config = require \Msqphp\Environment::$config_path.'database.php';
            $dns = $config['type'].':host='.$config['host'].';port='.$config['port'].';dbname='.$config['name'].';charset='.$config['charset'].';';
            $username = $config['username'];
            $password = $config['password'];
            $params = $config['params'];
            static::$pdo = new \PDO($dns, $username, $password, $params);
            static::$pdo->exec('SET NAMES '.$config['charset']);
        }        
    }
    public function error()
    {
        return static::$pdo->errorInfo();
    }
    public function insert(string $table)
    {
        $this->sql['insert'] = $table;
        return $this;
    }
    public function field(string $field)
    {
        $this->sql['fields'][] = $field;
        return $this;
    }
    public function value($value)
    {
        $this->sql['values'][] = $value;
        return $this;
    }
    public function table($table)
    {
        $this->sql['table'][] = $table;
        return $this;
    }
    public function select()
    {
        $this->sql['select'] = func_get_args();
        return $this;
    }
    public function from()
    {
        $this->sql['from'] = func_get_args();
        return $this;
    }
    public function where()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                if ($args[0] === 1) {
                    $this->sql['where'][] = '1';
                    return $this;
                    break;
                }
                $where = $this->primary_key;
                $condition = '=';
                $value = $args[0];
                break;
            case 2:
                $where = $args[0];
                $condition = '=';
                $value = $args[1];
                break;
            case 3:
                $where = $args[0];
                $condition = $ars[1];
                $value = $args[2];
                break;
            default:
                throw new ModelException('不合理的where查询', 1);
                break;
        }

        $this->sql['where'][] = array($where,$condition,$value);
        return $this;
    }
    public function innerJoin()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 2:
                $this->sql['inner_join'][ $args[0] ][] = $args[1];
                break;
            case 3:
                $this->sql['inner_join'][ $args[0] ][] = array($args[1],$args[2]);
                break;
            
            default:
                # code...
                break;
        }
        
        return $this;
    }
    public function order()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                $this->sql['order'][] = array($this->primary_key,$args[0]);
                break;
            case 2:
                $this->sql['order'][] = array($args[0],$args[1]);
                break;
            default:
                throw new ModelException('不合理的order排序', 1);
                break;
        }
        return $this;
    }
    public function exists() : bool
    {
        $info = $this->sql;
        $sql = 'SELECT `'.$info['fields'][0].'` FROM `'.$info['table'][0].'` WHERE `'.$info['fields'][0].'` = :'.$info['fields'][0];
        $stat = static::$pdo->prepare($sql);

// SELECT `user_name` FROM `User` WHERE `user_name` = 'kjjalkjf';
        $type = is_string($info['values'][0]) ? \PDO::PARAM_STR : \PDO::PARAM_INT;

        $stat->bindParam(':'.$info['fields'][0],$info['values'][0],$type);
        $stat->execute();
        $count = count($stat->fetchAll(2));
        $this->sql = [];
        return $count !== 0;
    }
    public function exec() : int
    {
        if (null !== $this->stat) {
            if (!$this->stat->execute()) {
                $error = $this->stat->errorInfo();
                throw new ModelException($this->lastSql().'预处理失败,原因:'.$error[2]);
            }
            $count = $this->stat->rowCount();
            $this->sql=[];
            $this->stat=null;
            return $count;
        } else {
            $sql = $this->queryAssembly();
            $this->sql=[];
            return static::$pdo->exec($sql);
        }
    }
    public function findColumn()
    {
        if (null === $this->stat) {
            $sql = $this->queryAssembly();
            $stat = static::$pdo->query($sql);
        } else {
            $stat = $this->stat;
            $stat->execute();
        }

        // $result = $stat->fetch(\PDO::FETCH_NUM);
        $result = $stat->fetch(3);

        $this->stat = null;
        $this->sql = [];
        return $result[0] ?? '';
    }    
    public function findOne()
    {
        if (null === $this->stat) {
            $sql = $this->queryAssembly();
            $stat = static::$pdo->query($sql);
        } else {
            $stat = $this->stat;
            $stat->execute();
        }
        

        $result = $stat->fetch(2);
        $this->stat = null;
        $this->sql = [];
        return $result;
    }
    public function findAll()
    {
        if (null === $this->stat) {
            $sql = $this->queryAssembly();
            $stat = static::$pdo->query($sql);
        } else {
            $stat = $this->stat;
            $stat->execute();
        }
        

        $result = $stat->fetchAll(2);
        $this->stat = null;
        $this->sql = [];
        return $result;
    }
    

    public function prepare()
    {
        $sql = $this->queryAssembly();
        $this->stat = static::$pdo->prepare($sql);

        return $this;
    }
    public function bindParam($bind,$value,$type)
    {
        $bool = $this->stat->bindParam($bind,$value,$type);
        return $this;
    }


    public function lastInsertId() {
        return static::$pdo->lastInsertId();
    }


    private function queryAssembly() : string
    {
        $sql = $this->sql;
        $str = '';

        if (isset($sql['select'])) {
            $str = 'SELECT ';
            foreach ($sql['select'] as $value) {
                $str.='`'.$value.'`,';
            }
            $str = rtrim($str,',') . ' ';
            if (isset($sql['from'])) {
                $str .= 'FROM ';
                foreach ($sql['from'] as $value) {
                    $str.='`'.$value.'`,';
                }
                $str = rtrim($str,',') . ' ';
            }
            if (isset($sql['where'])) {
                $str .= 'WHERE ';
                foreach ($sql['where'] as $value) {
                    if (is_string($value)) {
                        $str.=$value.' and ';
                    } else {
                        $str.='`'.$value[0].'` '. $value[1].$value[2] . ' and ';
                    }
                }
                $str = substr($str,0,strlen($str)-4);
            }
            if (isset($sql['inner_join'])) {
                foreach ($sql['inner_join'] as $key => $value) {
                    $str .= 'INNER JOIN '.'`'.$key.'`'.' ON ';
                    foreach ($value as $v) {
                        if (is_string($v)) {
                            $str .= $v.' AND ';
                        } else {
                            $str .= '`'.$this->table.'`.`'.$v[0].'` = `'.$key.'`.`'.$v[1].'` AND ';
                        }                    
                    }
                    $str = substr($str,0,strlen($str)-4);
                }
            }
            if (isset($sql['order'])) {
                $str .= 'ORDER BY ';
                foreach ($sql['order'] as $value) {
                    $str .= '`'.$value[0].'` '.$value[1].' ';
                }
            }
        }

        if (isset($sql['insert'])) {
            $str = 'INSERT INTO `'.$sql['insert'].'` ';
            if (isset($sql['fields'])) {
                $str .= '(';
                foreach ($sql['fields'] as $field) {
                    $str.='`'.$field.'`,';
                }
                $str = rtrim($str,',').') ';
            }
            if (isset($sql['values'])) {
                $str .= 'VALUES (';
                foreach ($sql['values'] as $value) {
                    $str.=$value.',';
                }
                $str = rtrim($str,',').') ';
            }
        }

        $this->sqls[] = $this->sql['sql'] = $str;
        return $str;
    }
    public function lastSql()
    {
        return $this->sqls[count($this->sqls)-1];
    }
}