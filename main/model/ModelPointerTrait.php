<?php declare(strict_types = 1);
namespace msqphp\main\model;

trait ModelPointerTrait
{
    protected $pointer = [];

    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }

    private function getTrueTable(string $table) : string
    {
        return $table[0] === '`' ? $table : '`'.$this->getPrefix().$table.'`';
    }
    private function getPrefix() : string
    {
        return $this->pointer['prefix'] ?? static::$config['prefix'];
    }
    private function getTrueField(string $field) : string
    {
        return $field !== '*' ? '`'.$field.'`' : $field;
    }
    private function getPrepare() : array
    {
        return $this->pointer['prepare'] ?? [];
    }

    private function addPrepare($value, string $type) : string
    {
        switch (strtolower($type)) {
            case 'int':
                $type = \PDO::PARAM_INT;
                break;
            case 'str':
            case 'string':
                $type = \PDO::PARAM_STR;
                break;
            default:
                static::exception('未知类型');
        }
        $pre_name = ':prepare' . (string) count($this->pointer['prepare'] ?? []);
        $this->pointer['prepare'][$pre_name] = [$value, $type];
        return $pre_name;
    }


    public function field() : self
    {
        foreach (func_get_args() as $field) {
            $this->pointer['field'][] = $this->getTrueField($field);
        }
        return $this;
    }
    public function value($value, ?string $type = null) : self
    {
        if (is_array($value)) {
            $this->pointer['value'][] = $this->addPrepare($value[0], $value[1]);
        } elseif (null !== $type) {
            $this->pointer['value'][] = $this->addPrepare($value, $type);
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
        foreach (func_get_args() as $table) {
            $this->pointer['table'][] = $this->getTrueTable($table);
        }
        return $this;
    }
    public function join(string $type, string $table) : self
    {
        $this->pointer['join']['type'] = $type;
        $this->pointer['join']['table']= $this->getTrueTable($table);
        return $this;
    }
    public function innerJoin(string $table) : self
    {
        return $this->join('inner_join', $table);
    }
    public function leftJoin(string $table) : self
    {
        return $this->join('left_join', $table);
    }
    public function rightJoin(string $table) : self
    {
        return $this->join('right_join', $table);
    }
    public function fullJoin(string $table) : self
    {
        return $this->join('full_join', $table);
    }
    public function crossJoin(string $table) : self
    {
        return $this->join('cross_join', $table);
    }

    public function on(string $left, ?string $right = null) : self
    {
        $args = func_get_args();
        switch (count($args)) {
            case 1:
                $this->pointer['join']['on'][] = [$this->getTrueField($args[0]), '=', $this->getTrueField($args[0])];
                break;
            case 2;
                $this->pointer['join']['on'][] = [$this->getTrueField($args[0]), '=', $this->getTrueField($args[1])];
                break;
            case 3:
                $this->pointer['join']['on'][] = [$this->getTrueField($args[0]), $args[1], $this->getTrueField($args[2])];
                break;
            default:
                static::exception('错误的传递参数个数');
        }
        return $this;
    }
    public function where()
    {
        $this->pointer['where'][] = $this->getWhereOrHavingInfo(func_get_args());
        return $this;
    }
    private function getWhereOrHavingInfo(array $args) : array
    {
        $where = $this->getTrueField(array_shift($args));
        switch (count($args)) {
            case 2:
                $condition = array_shift($args);
            case 1:
                $condition = $condition ?? '=';
                if ($condition === 'in') {
                    $value = is_string($args[0]) ? '(\''. implode('\',\'', $args[0]).'\')' : '('. implode(',', $args[0]).')';
                } else {
                    $value = is_array($args[0]) ? $this->addPrepare($args[0][0], $args[0][1]) : $args[0];
                }
                break;
            default:
                throw new ModelException('不合理的where|having查询');
        }
        return [$where, $condition, $value];
    }
    public function count() : self
    {
        foreach (func_get_args() as $filed) {
            $this->pointer['field'][] = 'count('.$this->getTrueField($field).')';
        }
        return $this;
    }
    public function group(string $field)
    {
        $this->pointer['group'][] = $field;
        return $this;
    }
    public function having() : self
    {
        $this->pointer['having'][] = $this->getWhereOrHavingInfo(func_get_args());
        return $this;
    }
    public function order(string $field, string $type = 'ASC') : self
    {
        $this->pointer['order'][] = ['field'=>$this->getTrueField($field), 'type'=>strtolower($type) === 'desc' ? 'DESC' : 'ASC'];
        return $this;
    }
    public function limit(int $num1, ?int $num2 = null) : self
    {
        $this->pointer['limit'] = $num2 === null ? [0, $num1] : [$num1, $num2];
        return $this;
    }
}