<?php namespace Proxifier\DBHandlers;

use Molecule\ORM;
use PDOStatement;

abstract class DBHandler
{
    protected $result;

    /** @var PDOStatement */
    protected $statement;

    protected $select;
    protected $where;
    protected $order;

    protected const table = null;

    public function __construct(ORM $table)
    {
        $this->statement = $table->where($this->where ?? null)->order($this->order)->select($this->select)->exec();
    }

    abstract public function __invoke(): array;
}