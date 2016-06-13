<?php declare(strict_types = 1);
namespace msqphp\core\model;

use msqphp\core;


trait ModelPointerTrait
{
    protected $pointer = [];

    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function field() : self
    {
        array_map(function (string $field) {
            $this->pointer['field'][] = $field !== '*' ? '`'.$field.'`' : $field;
        }, func_get_args());
        return $this;
    }
    public function value($value, $type = null) : self
    {
        if (null === $type) {
            $pre_name = ':prepare' . (string) count($this->pointer['prepare'] ?? []);
            if (is_string($type)) {
                $type = 'int' === strtolower($type) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            }
            $this->pointer['prepare'][$pre_name] = [$value, $type];
            $this->pointer['value'][] = $pre_name;
        } else {
            $this->pointer['value'][] = $value;
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
        $prefix = $this->pointer['prefix'] ?? $this->config['prefix'];
        array_map(function (string $table) use ($prefix) {
            $this->pointer['table'][] = '`'.$prefix.$table.'`';
        }, func_get_args());
        return $this;
    }
    public function where()
    {
        $args = func_get_args();
        $where = $args[0];
        array_shift($args);
        switch (count($args)) {
            case 2:
                $condition = $args[0];
                array_shift($args);
            case 1:
                $condition = $condition ?? '=';
                if (is_array($args[0])) {
                    $pre_name = ':prepare' . (string) count($this->pointer['prepare'] ?? []);
                    if (is_string($args[0][1])) {
                        $args[0][1] = 'int' === strtolower($args[0][1]) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
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

        $this->pointer['where'][] = [$args[0], $condition, $value];
        return $this;
    }
    public function count() : self
    {
        array_map(function (string $field) {
            $this->pointer['field'][] = 'count('.($field !== '*' ? '`'.$field.'`' : $field).')';
        }, func_get_args());
        return $this;
    }
    public function group(string $field, string $type = 'asc')
    {
        $this->pointer['group'][] = [$field, $type];
        return $this;
    }
    public function having()
    {
        $args = func_get_args();
        $having = $args[0];
        array_shift($args);
        switch (count($args)) {
            case 2:
                $condition = $args[0];
                array_shift($args);
            case 1:
                $condition = $condition ?? '=';
                if (is_array($args[0])) {
                    $pre_name = ':prepare' . (string) count($this->pointer['prepare'] ?? []);
                    if (is_string($args[0][1])) {
                        $args[0][1] = 'int' === strtolower($args[0][1]) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
                    }
                    $this->pointer['prepare'][$pre_name] = [$args[2][0], $args[2][1]];
                    $value = $pre_name;
                } else {
                    $value = $args[2];
                }
                break;
            default:
                throw new ModelException('不合理的having查询', 1);
        }

        $this->pointer['having'][] = [$args[0], $condition, $value];
        return $this;
    }
    public function order(string $filed, string $type = 'ASC')
    {
        $this->pointer['order'][] = ['filed'=>($filed === '*' ? $filed : '`'.$filed.'`'), 'type'=>strtolower($type) === 'desc' ? 'DESC' : 'ASC'];
        return $this;
    }
    public function limit(int $max)
    {
        $this->pointer['limit'] = $max;
        return $this;
    }
}