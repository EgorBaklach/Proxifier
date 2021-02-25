<?php namespace Proxifier\DBHandlers;

class Proxies extends DBHandler
{
    protected $select = ['id', 'ip', 'port', 'user', 'pass'];
    protected $where = ['type=' => 'mobile', 'active=' => 'Y'];
    protected $order = ['last_request' => 'asc', 'id' => 'asc'];

    const table = 'proxies';

    public function __invoke(): array
    {
        $key = 0;

        while($value = $this->statement->fetch())
        {
            $this->result[$value['key'] ?: $key++] = $value;
        }

        return $this->result;
    }
}