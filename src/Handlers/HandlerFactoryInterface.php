<?php namespace Proxifier\Handlers;

use Molecule\ORM;

interface HandlerFactoryInterface
{
    public function handler(string $name, string $handler): HandlerFactoryInterface;
    public function manager(array $handlers): HandlerFactoryInterface;
    public function unpack(string $name): ?array;
    public function table(string $name): ORM;
}