<?php namespace Proxifier\Controllers;

abstract class ControllerAbstract implements ControllerInterface
{
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
}
