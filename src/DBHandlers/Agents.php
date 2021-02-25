<?php namespace Proxifier\DBHandlers;

class Agents extends DBHandler
{
    protected $select = ['*'];
    protected $where = null;
    protected $order = ['id'];

    const table = 'agents';

    public function __invoke(): array
    {
        while($value = $this->statement->fetch())
        {
            $this->result[$value['type']][] = $value;
        }

        return $this->result;
    }
}