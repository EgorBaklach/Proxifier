<?php namespace Proxifier\Handlers;

use Molecule\ORM;
use PDOStatement;

abstract class HandlerAbstract implements HandlerInterface
{
    /** @var PDOStatement */
    protected $statement;

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
        $this->hash = $hash.'_'.md5(static::class); $this->statement = $table->where(static::where)->order(static::order)->select(static::select)->exec();
    }

    public function conditions(): array
    {
        return [$this->hash, 86400, [$this, 'handle']];
    }
}