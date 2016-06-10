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
}