<?php declare(strict_types = 1);
namespace msqphp\core\model;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

abstract class Model
{
    protected $pointer = [];

    public function __construct()
    {
        core\database\Database::connect();
    }



###
#  指针
###
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function field() : self
    {
        foreach (func_get_args() as $field) {
            $this->pointer['field'][] = $field !== '*' ? '`'.$field.'`' : $field;
        }
        return $this;
    }
    public function value() : self
    {
        $args = func_get_args();
        switch (count($args)) {
            case 2:
                $number = isset($this->pointer['prepare']) ? count($this->pointer['prepare']) : 0;
                $pre_name = ':prepare' . (string) $number;
                if (is_string($args[1])) {
                    if (0 === strcasecmp('int', $args[1])) {
                        $args[1] = \PDO::PARAM_INT;
                    } else {
                        $args[1] = \PDO::PARAM_STR;
                    }
                }
                $this->pointer['prepare'][$pre_name] = [$args[0], $args[1]];
                $this->pointer['value'][] = $pre_name;
                break;
            case 1:
                $this->pointer['value'][] = $args[0];
                break;
            default:
                throw new ModelException('未知错误');
        }
        return $this;
    }
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    public function table() : self
    {
        $prefix = $this->pointer['prefix'] ?? core\config\Config::get('model.prefix');
        foreach (func_get_args() as $table) {
            $this->pointer['table'][] = '`'.$prefix.$table.'`';
        }
        return $this;
    }
    public function where()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 2:
                $where = $args[0];
                $condition = '=';
                if (is_array($args[1])) {
                    $number = isset($this->pointer['prepare']) ? count($this->pointer['prepare']) : 0;
                    $pre_name = ':prepare' . (string) $number;
                    if (is_string($args[1][1])) {
                        if (0 === strcasecmp('int', $args[1][1])) {
                            $args[1][1] = \PDO::PARAM_INT;
                        } else {
                            $args[1][1] = \PDO::PARAM_STR;
                        }
                    }
                    $this->pointer['prepare'][$pre_name] = [$args[1][0], $args[1][1]];
                    $value = $pre_name;
                } else {
                    $value = $args[1];
                }
                break;
            case 3:
                $where = $args[0];
                $condition = $args[1];
                if (is_array($args[2])) {
                    $number = isset($this->pointer['prepare']) ? count($this->pointer['prepare']) : 0;
                    $pre_name = ':prepare' . (string) $number;
                    if (is_string($args[2][1])) {
                        if (0 === strcasecmp('int', $args[2][1])) {
                            $args[2][1] = \PDO::PARAM_INT;
                        } else {
                            $args[2][1] = \PDO::PARAM_STR;
                        }
                    }
                    $this->pointer['prepare'][$pre_name] = [$args[2][0], $args[2][1]];
                    $value = $pre_name;
                } else {
                    $value = $args[2];
                }
                break;
            default:
                throw new ModelException('不合理的where查询', 1);
        }

        $this->pointer['where'][] = [$where, $condition, $value];
        return $this;
    }
    public function count() : self
    {
        foreach (func_get_args() as $field) {
            $this->pointer['field'][] = 'count('.($field !== '*' ? '`'.$field.'`' : $field).')';
        }
        return $this;
    }
    public function group()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                $this->pointer['group'][] = [$this->primary_key, $args[0]];
                break;
            case 2:
                $this->pointer['group'][] = [$args[0], $args[1]];
                break;
            default:
                throw new ModelException('不合理的group分组', 1);
        }
        return $this;
    }
    public function having()
    {
        $args = func_get_args();
        switch (count($args)) {
            case 2:
                $having = $args[0];
                $condition = '=';
                if (is_array($args[1])) {
                    $value = $args[1][0];
                    $this->pointer['prepare'][$value] = [$args[1][1],$args[1][2]];
                } else {
                    $value = $args[1];
                }
                break;
            case 3:
                $having = $args[0];
                $condition = $args[1];
                if (is_array($args[2])) {
                    $value = $args[2][0];
                    $this->pointer['prepare'][$value] = [$args[2][1],$args[2][2]];
                } else {
                    $value = $args[2];
                }
                break;
            default:
                throw new ModelException('不合理的having查询', 1);
        }

        $this->pointer['having'][] = [$having, $condition, $value];
        return $this;
    }
    public function order(string $filed, string $type)
    {
        $this->pointer['order'][] = [($filed === '*' ? $filed : '`'.$filed.'`'), $type];
        return $this;
    }
    public function limit(int $max)
    {
        $this->pointer['limit'] = $max;
        return $this;
    }







###
#  操作
###
    public function query(string $sql)
    {
        return core\database\Database::query($sql);
    }
    public function exec(string $sql)
    {
        return core\database\Database::exec($sql);
    }
    public function exists() : bool
    {
        return '' === core\database\Database::getColumn($this->getSelectQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function getOne()
    {
        $this->limit(1);
        return core\database\Database::getOne($this->getSelectQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function getColumn()
    {
        $this->limit(1);
        return core\database\Database::getColumn($this->getSelectQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function get()
    {
        return core\database\Database::get($this->getSelectQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function add()
    {
        return core\database\Database::exec($this->getInsertQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function set()
    {

    }
    public function update()
    {
        return core\database\Database::exec($this->getUpdateQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function delete()
    {
        return core\database\Database::exec($this->getDeleteQuery(), ($this->pointer['prepare'] ?? []));
    }
    public function make()
    {

    }

    public function begin()
    {
        core\database\Database::beginTransaction();
    }
    public function beginTransaction()
    {
        core\database\Database::beginTransaction();
    }
    public function commit()
    {
        core\database\Database::commit();
    }
    public function rollBack()
    {
        core\database\Database::rollBack();
    }
    public function cancel()
    {
        core\database\Database::rollBack();
    }
    public function end()
    {
        core\database\Database::commit();
    }
    public function lastInsertId() : int
    {
        return core\database\Database::lastInsertId();
    }
    private function getInsertQuery() : string
    {
        $pointer = $this->pointer;
        $sql = 'INSERT INTO '.$pointer['table'][0];
        if (isset($pointer['field'])) {
            $sql .= '(';
            foreach ($pointer['field'] as $field) {
                $sql.=$field.', ';
            }
            $sql = rtrim($sql, ', ').') ';
        }
        if (isset($pointer['value'])) {
            $sql .= 'VALUES (';
            foreach ($pointer['value'] as $value) {
                $sql.=$value.', ';
            }
            $sql = rtrim($sql, ', ').') ';
        }
        return $sql;
    }
    private function getSelectQuery() : string
    {
        $pointer = $this->pointer;
        $sql = 'SELECT ';
        if (isset($pointer['field'])) {
            foreach ($pointer['field'] as $value) {
                $sql.=$value.', ';
            }
            $sql = rtrim($sql, ', ') . ' ';
        } else {
            throw new ModelException('错误的sql查询,未指定查询值');
        }
        if (isset($pointer['table'])) {
            $sql .= 'FROM ';
            foreach ($pointer['table'] as $value) {
                $sql.=$value.', ';
            }
            $sql = rtrim($sql, ', ') . ' ';
        } else {
            throw new ModelException('错误的sql查询,未指定表名');
        }
        if (isset($pointer['where'])) {
            $sql .= 'WHERE ';
            foreach ($pointer['where'] as $value) {
                if (is_string($value)) {
                    $sql.=$value.' and ';
                } else {
                    $sql.='`'.$value[0].'` '. $value[1].$value[2] . ' and ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else {
            $sql .= 'WHERE 1 ';
        }
        if (isset($pointer['inner_join'])) {
            foreach ($pointer['inner_join'] as $key => $value) {
                $sql .= 'INNER JOIN '.'`'.$key.'`'.' ON ';
                foreach ($value as $v) {
                    if (is_string($v)) {
                        $sql .= $v.' AND ';
                    } else {
                        $sql .= '`'.$this->table.'`.`'.$v[0].'` = `'.$key.'`.`'.$v[1].'` AND ';
                    }
                }
                $sql = substr($sql, 0, strlen($sql)-4);
            }
        }
        if (isset($pointer['having'])) {
            $sql .= 'HAVING ';
            foreach ($pointer['having'] as $value) {
                if (is_string($value)) {
                    $sql.=$value.' and ';
                } else {
                    $sql.='`'.$value[0].'` '. $value[1].$value[2] . ' and ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        }
        if (isset($pointer['order'])) {
            $sql .= 'ORDER BY ';
            foreach ($pointer['order'] as $value) {
                $sql .= $value[0].' '.$value[1].' ';
            }
        }
        if (isset($pointer['limit'])) {
            $sql .= 'LIMIT '.$pointer['limit']. ' ';
        }
        return $sql;
    }
    private function getUpdateQuery() : string
    {
        $sql = 'UPDATE ';
        $pointer = $this->pointer;
        if (isset($pointer['table']) && 1 === count($pointer['table'])) {
            $sql .= $pointer['table'][0];
        } else {
            throw new ModelException('错误的sql更新语句,未指定表名');
        }
        $sql .= 'SET ';
        if (isset($pointer['field']) && isset($pointer['value']) && count($pointer['field']) === $count = count($pointer['value']) ) {
            for ($i = 0; $i < $count; ++$i) {
                $sql .= $pointer['field'][$i] . '=' . $pointer['value'][$i] . ', ';
            }
            $sql = rtrim($sql, ', ') . ' ';
        } else {
            throw new ModelException('错误的sql更新语句,键值不匹配');
        }
        if (isset($pointer['where'])) {
            $sql .= 'WHERE ';
            foreach ($pointer['where'] as $value) {
                if (is_string($value)) {
                    $sql.=$value.' and ';
                } else {
                    $sql.='`'.$value[0].'` '. $value[1].$value[2] . ' and ';
                }
            }
            $sql = substr($sql, 0, strlen($sql)-4);
        } else {
            throw new ModelException('错误的sql更新语句,where键值不匹配');
        }
        return $sql;
    }
    private function getDeleteQuery() : string
    {
    }
}