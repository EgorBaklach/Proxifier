<?php namespace Proxifier\Handlers;

use Molecule\ORM;
use PDOStatement;

abstract class HandlerAbstract implements HandlerInterface
{
    /** @var PDOStatement */
    protected $statement;

    /** @var ORM */
    private $table;

    /** @var string */
    protected $hash;

    /** @var array */
    protected $result;

    /** @var array */
    protected const where = null;

    /** @var array */
    protected const order = [];

    /** @var array */
    protected const select = [];

    public function __construct(ORM $table, string $hash)
    {
        $this->table = $table; $this->hash = $hash.'_'.md5(static::class);
    }

    public function conditions(): array
    {
        $this->statement = $this->table->where(static::where)->order(static::order)->select(static::select)->exec(); return [$this->hash, 86400, [$this, 'handle']];
    }
}