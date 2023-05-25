<?php namespace Proxifier\Handlers;

class Agents extends HandlerAbstract
{
    protected const where = null;
    protected const order = ['id'];
    protected const select = ['*'];

    public function handle(): array
    {
        while($value = $this->statement->fetch()) $this->result[$value['type']][] = $value; return $this->result;
    }
}