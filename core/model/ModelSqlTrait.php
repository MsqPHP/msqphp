<?php declare(strict_types = 1);
namespace msqphp\core\model;

trait ModelSqlTrait
{
    private function getInsertQuery() : string
    {
        $pointer = $this->pointer;
        if (isset($pointer['field']) && isset($pointer['value']) && count($pointer['field']) === count($pointer['value'])) {
            $sql = 'INSERT INTO '.$pointer['table'][0] . '('.rtrim(implode(',', $pointer['field']), ',') . ') ' . 'VALUES ('. rtrim(implode(',', $pointer['value']), ',').') ';
        } else {
            throw new ModelException('错误的sql插入语句,键值不存在或数目不匹配');
        }
        return $sql;
    }
    private function getSelectQuery() : string
    {
        $pointer = $this->pointer;
        $sql = 'SELECT ';
        if (isset($pointer['field'])) {
            $sql .= rtrim(implode(',', $pointer['field']), ',') . ' ';
        } else {
            throw new ModelException('错误的sql查询,未指定查询值');
        }
        if (isset($pointer['table'])) {
            $sql .= 'FROM '.rtrim(implode(',', $pointer['table']), ',') . ' ';
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
            array_map(function (array $order) use (& $sql) {
                $sql .= $order['filed'].' '.$order['type'].' ';
            }, $pointer['order']);
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