<?php declare(strict_types = 1);
namespace msqphp\main\model;

use msqphp\core\database\Database;

trait ModelOperateTrait
{
    public function query()
    {
        return Database::query($this->getHandler(), $this->getSql(), $this->getPrepare());
    }
    public function exec() : ?int
    {
        return Database::exec($this->getHandler(), $this->getSql(), $this->getPrepare());
    }
    public function exists() : bool
    {
        return null !== Database::getColumn($this->getHandler(), $this->getSql('exists'), $this->getPrepare());
    }
    public function getOne()
    {
        return Database::getOne($this->getHandler(), $this->limit(1)->getSql('select'), $this->getPrepare());
    }
    public function getColumn()
    {
        return Database::getColumn($this->getHandler(), $this->limit(1)->getSql('select'), $this->getPrepare());
    }
    public function get()
    {
        return Database::get($this->getHandler(), $this->getSql('select'), $this->getPrepare());
    }
    public function add()
    {
        return Database::exec($this->getHandler(), $this->getSql('insert'), $this->getPrepare());
    }
    public function set()
    {

    }
    public function update()
    {
        return Database::exec($this->getHandler(), $this->getSql('update'), $this->getPrepare());
    }
    public function delete()
    {
        return Database::exec($this->getHandler(), $this->getSql('delete'), $this->getPrepare());
    }
    public function transaction(\Closure $func, array $args = [])
    {
        try {
            Database::beginTransaction($this->getHandler());
            $result = call_user_func($func, $args);
            Database::commit($this->getHandler());
            return $result;
        } catch (ModelException | DatabaseException $e) {
            Database::rollBack($this->getHandler());
            throw $e;
        }
    }
    public function lastInsertId() : int
    {
        return Database::lastInsertId($this->getHandler());
    }
    protected function getSql(string $type) : string
    {
        if (isset($this->pointer['sql'])) {
            return $this->pointer['sql'];
        }
        switch ($type) {
            case 'insert':
                return static::buildUpdateSql($this->pointer);
            case 'delete':
                return static::bulidDeleteSql($this->pointer);
            case 'exists':
                return static::buildExistsSql($this->pointer);
            case 'update':
                return static::buildUpdateSql($this->pointer);
            case 'select':
            default:
                return static::buildSelectSql($this->pointer);
        }
    }
    private function getHandler() : string
    {
        return $this->pointer['handler'] ?? static::$config['default_handler'];
    }
}