<?php namespace Proxifier\Handlers;

interface HandlerInterface
{
    public function conditions(): array;
    public function handle(): array;
}