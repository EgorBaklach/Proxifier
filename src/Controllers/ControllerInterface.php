<?php namespace Proxifier\Controllers;

interface ControllerInterface
{
    public function __invoke($node, $info, $data, $attributes);
}
