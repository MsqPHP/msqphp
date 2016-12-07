<?php declare(strict_types = 1);
namespace msqphp\main\model;

trait ModelSqlTrait
{
    protected function getSql(string $type) : string
    {
        switch ($type) {
            case 'exists':
                return $this->getExistsQuery();
            case 'select':
            default:
                return $this->getSelectQuery();
        }
    }

    private function getExistsQuery() : string
    {
        $pointer = $this->pointer;
        // 开始
        $sql = 'SELECT ';
        isset($pointer['field']) || $this->exception('错误的sql语句,未指定查询值');
        // 以逗号分割字段,并移除最后的逗号,加一个空格.
        $sql .= rtrim(implode(',', $pointer['field']), ',') . ' ';
        isset($pointer['table']) || $this->exception('错误的sql语句,未指定表名');
        // 以逗号分割表名,并移除最后的逗号,加一个空格.
        $sql .= 'FROM '.rtrim(implode(',', $pointer['table']), ',') . ' ';

        isset($pointer['value']) || $this->exception('错误的sql语句,未指定条件');
        // WHERE
        $sql .= 'WHERE ';
        // 如果字段数小于值,无法判断
        (count($pointer['field']) >= $l = count($pointer['value'])) || $this->exception('错误的sql语句,指定whrer条件错误');
        // where值
        for ($i = 0; $i < $l; ++$i) {
            $sql .= $pointer['field'][$i] . '=' . $pointer['value'][$i] . ' AND ';
        }
        // 移除最后的 and四字符
        $sql = substr($sql, 0, -4);
        return $sql;
    }

    private function getInsertQuery() : string
    {
        $pointer = $this->pointer;
        (isset($pointer['field']) && isset($pointer['value']) && count($pointer['field']) === count($pointer['value'])) || $this->exception('错误的sql插入语句,键值不存在或数目不匹配');
        (isset($pointer['table']) && 1 === count($pointer['table'])) || $this->exception('错误的sql插入语句,未指定表名,或者指定表过多');
        return 'INSERT INTO '.$pointer['table'][0] . ' ('.rtrim(implode(',', $pointer['field']), ',') . ') ' . 'VALUES ('. rtrim(implode(',', $pointer['value']), ',').') ';
    }
    private function getSelectQuery() : string
    {
        $pointer = $this->pointer;
        // 开始
        $sql = 'SELECT ';
        isset($pointer['field']) || $this->exception('错误的sql语句,未指定查询值');
        // 以逗号分割字段,并移除最后的逗号,加一个空格.
        $sql .= rtrim(implode(',', $pointer['field']), ',') . ' ';
        isset($pointer['table']) || $this->exception('错误的sql语句,未指定表名');
        // 以逗号分割表名,并移除最后的逗号,加一个空格.
        $sql .= 'FROM '.rtrim(implode(',', $pointer['table']), ',') . ' ';

        if (isset($pointer['join'])) {
            switch ($pointer['join']['type']) {
                case 'inner_join':
                    $sql .= 'INNER';
                    break;
                case 'left_join':
                    $sql .= 'LEFT';
                    break;
                case 'right_join':
                    $sql .= 'RIGHT';
                    break;
                case 'full_join':
                    $sql .= 'FULL';
                    break;
                case 'cross_join':
                    $sql .= 'CROSS';
                    break;
                default:
                    $this->exception('未知的join语句类型');
            }
            $table = $pointer['join']['table'];
            $sql .=  ' JOIN '.$table.' ON ';
            foreach ($pointer['join']['on'] as $v) {
                $sql .= $pointer['table'][0].'.'.$v[0].$v[1].$table.'.'.$v[2].' AND ';
            }
            $sql = substr($sql, 0, -4);
        }
        if (isset($pointer['where'])) {
            $sql .= 'WHERE ';
            // 添加where值
            foreach ($pointer['where'] as [$having, $condition, $value]) {
                $sql .= $having . $condition . $value . ' AND ';
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql .= 'WHERE 1 ';
        }
        if (isset($pointer['having'])) {
            $sql .= 'HAVING ';
            // 添加where值
            foreach ($pointer['having'] as [$having, $condition, $value]) {
                $sql .= $having . $condition . $value . ' AND ';
            }
            $sql = substr($sql, 0, -4);
        }
        if (isset($pointer['order'])) {
            $sql .= 'ORDER BY ';
            foreach ($pointer['order'] as ['field'=>$filed, 'type' => $type]) {
                $sql .= $filed.' '.$type.',';
            }
            $sql = substr($sql, 0, -1) . ' ';
        }
        if (isset($pointer['limit'])) {
            $sql .= 'LIMIT '.$pointer['limit'][0].','.$pointer['limit'][1].' ';
        }

        return $sql;
    }
    private function getUpdateQuery() : string
    {
        $pointer = $this->pointer;
        (isset($pointer['table']) && 1 === count($pointer['table'])) || $this->exception('错误的sql更新语句,未指定表名,或者指定表过多');
        // UPDATE `表名` SET
        $sql = 'UPDATE ' . $pointer['table'][0] . 'SET ';
        // 字段=值........
        (isset($pointer['field']) && isset($pointer['value']) && count($pointer['field']) === $count = count($pointer['value'])) || $this->exception('错误的sql更新语句,键值不匹配');
        for ($i = 0; $i < $count; ++$i) {
            $sql .= $pointer['field'][$i] . '=' . $pointer['value'][$i] . ', ';
        }
        // 移除末尾, 然后添加where判断
        $sql = substr($sql, 0, -2) . ' WHERE ';
        isset($pointer['where']) || $this->exception('错误的sql更新语句,where键不存在');
        // 添加where值
        foreach ($pointer['where'] as $value) {
            $sql .= $value[0] . $value[1] . $value[2] . ' AND ';
        }
        // 去除最后的 AND
        return substr($sql, 0, -4);
    }
    private function getDeleteQuery() : string
    {
    }
}