<?php namespace Proxifier\Sources;

interface SourceInterface
{
    public function start();
    public function set(string $url, $queries, $headers, $proxy, $data, array $options = null);
    public function exec(callable $controller, callable $throwback);
}
