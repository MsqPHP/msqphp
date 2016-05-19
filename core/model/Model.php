<?php declare(strict_types = 1);
namespace msqphp\core\model;

use msqphp\base;
use msqphp\core;

abstract class Model
{
    //db处理类
    static protected $db = null;

    protected $sql = [];

    public function __construct()
    {
        if (null === static::$db) {
            static::$db = core\database\Database::getInstance();
        }
    }

    public function init() : self
    {
        $this->sql = [];
        return $this;
    }
    public function field() : self
    {
        foreach (func_get_args() as $field) {
            if ($field !== '*') {
                $field = '`'.$field.'`';
            }
            $this->sql['field'][] = $field;
        }
        return $this;
    }
    public function value() : self
    {
        $args = func_get_args();
        switch (count($args)) {
            case 3:
                $this->sql['prepare'][$args[0]] = [$args[1],$args[2]];
            case 2:
                $this->sql['prepare'][$args[0]] = $this->sql['prepare'][$args[0]] ?? [$args[1],\PDO::PARAM_STR];
            case 1:
                $this->sql['value'][] = $args[0];
                break;
            default:
                throw new ModelException('未知错误');
        }
        return $this;
    }
    public function exists() : bool
    {
        return '' === static::$db::getColumn($this->getSelectQuery(), ($this->sql['prepare'] ?? []));
    }
    public function where()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                if ($args[0] === 1) {
                    $this->sql['where'][] = '1';
                    return $this;
                }
                $where = $this->primary_key;
                $condition = '=';
                $value = $args[0];
                break;
            case 2:
                $where = $args[0];
                $condition = '=';
                if (is_array($args[1])) {
                    $value = $args[1][0];
                    $this->sql['prepare'][$value] = [$args[1][1],$args[1][2]];
                } else {
                    $value = $args[1];
                }
                break;
            case 3:
                $where = $args[0];
                $condition = $args[1];
                if (is_array($args[2])) {
                    $value = $args[2][0];
                    $this->sql['prepare'][$value] = [$args[2][1],$args[2][2]];
                } else {
                    $value = $args[2];
                }
                break;
            default:
                throw new ModelException('不合理的where查询', 1);
        }

        $this->sql['where'][] = [$where, $condition, $value];
        return $this;
    }
    public function table(string $table) : self
    {
        $this->sql['table'][] = '`'.($this->sql['prefix'] ?? core\config\Config::get('model.prefix')).$table.'`';
        return $this;
    }
    public function count() : self
    {
        $count = $this->sql['count'] ?? [];
        foreach (func_get_args() as $field) {
            if ($field !== '*') {
                $field = '`'.$field.'`';
            }
            $this->sql['field'][] = 'count('.$field.')';
        }
        return $this;
    }
    public function order()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                $this->sql['order'][] = [$this->primary_key, $args[0]];
                break;
            case 2:
                $this->sql['order'][] = [$args[0], $args[1]];
                break;
            default:
                throw new ModelException('不合理的order排序', 1);
        }
        return $this;
    }

    
    public function getOne()
    {
        return static::$db::getOne($this->getSelectQuery(), ($this->sql['prepare'] ?? []));
    }
    public function getColumn()
    {
        return static::$db::getColumn($this->getSelectQuery(), ($this->sql['prepare'] ?? []));
    }
    public function get()
    {
        return static::$db::get($this->getSelectQuery(), ($this->sql['prepare'] ?? []));
    }
    public function add()
    {
        return static::$db::exec($this->getInsertQuery(), ($this->sql['prepare'] ?? []));
    }
    public function set()
    {

    }
    public function update()
    {

    }
    public function delete()
    {

    }
    public function make()
    {

    }

    public function begin()
    {
        static::$db::beginTransaction();
    }
    public function beginTransaction()
    {
        static::$db::beginTransaction();
    }
    public function commit()
    {
        static::$db::commit();
    }
    public function rollBack()
    {
        static::$db::rollBack();
    }
    public function cancel()
    {
        static::$db::rollBack();
    }
    public function end()
    {
        static::$db::commit();
    }
    public function lastInsertId() : int
    {
        return static::$db::lastInsertId();
    }
    private function getInsertQuery() : string
    {
        $sql = $this->sql;
        $query = 'INSERT INTO '.$sql['table'][0];
        if (isset($sql['field'])) {
            $query .= '(';
            foreach ($sql['field'] as $field) {
                $query.=$field.', ';
            }
            $query = rtrim($query, ', ').') ';
        }
        if (isset($sql['value'])) {
            $query .= 'VALUES (';
            foreach ($sql['value'] as $value) {
                $query.=$value.', ';
            }
            $query = rtrim($query, ', ').') ';
        }
        return $query;
    }
    public function getSelectQuery() : string
    {
        $sql = $this->sql;
        $query = 'SELECT ';
        foreach ($sql['field'] as $value) {
            $query.=$value.', ';
        }
        $query = rtrim($query, ', ') . ' ';
        if (isset($sql['table'])) {
            $query .= 'FROM ';
            foreach ($sql['table'] as $value) {
                $query.=$value.', ';
            }
            $query = rtrim($query, ', ') . ' ';
        }
        if (isset($sql['where'])) {
            $query .= 'WHERE ';
            foreach ($sql['where'] as $value) {
                if (is_string($value)) {
                    $query.=$value.' and ';
                } else {
                    $query.='`'.$value[0].'` '. $value[1].$value[2] . ' and ';
                }
            }
            $query = substr($query, 0, strlen($query)-4);
        }
        if (isset($sql['inner_join'])) {
            foreach ($sql['inner_join'] as $key => $value) {
                $query .= 'INNER JOIN '.'`'.$key.'`'.' ON ';
                foreach ($value as $v) {
                    if (is_string($v)) {
                        $query .= $v.' AND ';
                    } else {
                        $query .= '`'.$this->table.'`.`'.$v[0].'` = `'.$key.'`.`'.$v[1].'` AND ';
                    }                    
                }
                $query = substr($query, 0, strlen($query)-4);
            }
        }
        if (isset($sql['order'])) {
            $query .= 'ORDER BY ';
            foreach ($sql['order'] as $value) {
                $query .= '`'.$value[0].'` '.$value[1].' ';
            }
        }
        return $query;
    }
    public function getQuery() : string
    {
        $sql = $this->sql;
        show($sql);

        return $query;
    }
}