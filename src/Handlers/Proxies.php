<?php namespace Proxifier\Handlers;

class Proxies extends HandlerAbstract
{
    protected const where = ['active=' => 'Y'];
    protected const order = ['last_request' => 'asc', 'id' => 'asc'];
    protected const select = ['id', 'ip', 'port', 'user', 'pass', 'type'];

    public function handle(): array
    {
        while($value = $this->statement->fetch()) $this->result[$value['type']][] = $value; return $this->result;
    }
}