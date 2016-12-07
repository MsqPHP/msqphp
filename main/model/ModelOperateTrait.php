<?php declare(strict_types = 1);
namespace msqphp\main\model;

use msqphp\core;

trait ModelOperateTrait
{
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
        return null !== core\database\Database::getColumn($this->getExistsQuery(), $this->getPrepare());
    }
    public function getOne()
    {
        return core\database\Database::getOne($this->limit(1)->getSelectQuery(), $this->getPrepare());
    }
    public function getColumn()
    {
        return core\database\Database::getColumn($this->limit(1)->getSelectQuery(), $this->getPrepare());
    }
    public function get()
    {
        return core\database\Database::get($this->getSelectQuery(), $this->getPrepare());
    }
    public function add()
    {
        return core\database\Database::exec($this->getInsertQuery(), $this->getPrepare());
    }
    public function set()
    {

    }
    public function update()
    {
        return core\database\Database::exec($this->getUpdateQuery(), $this->getPrepare());
    }
    public function delete()
    {
        return core\database\Database::exec($this->getDeleteQuery(), $this->getPrepare());
    }
    public function transaction(\Closure $func, array $args = [])
    {
        try {
            core\database\Database::beginTransaction();
            $result = call_user_func($func, $args);
            core\database\Database::commit();
            return $result;
        } catch (ModelException | core\database\DatabaseException $e) {
            core\database\Database::rollBack();
            throw $e;
        }
    }
    public function lastInsertId() : int
    {
        return core\database\Database::lastInsertId();
    }
}