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

    //
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
}