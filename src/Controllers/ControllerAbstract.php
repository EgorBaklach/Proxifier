<?php namespace Proxifier\Controllers;

abstract class ControllerAbstract
{
    protected $callback;
    protected $counter;

    const json_type = 'application/json';
    const steps = 24;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function getContentType($value): string
    {
        if(!is_array($value)) $value = (array) $value;

        [$type, $charset] = array_map('trim', explode(';', array_shift($value)));

        return $type;
    }

    abstract public function __invoke($node, $info, $data, $attributes);
}
