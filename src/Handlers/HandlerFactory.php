<?php namespace Proxifier\Handlers;

use Contracts\Cache\RememberInterface;
use Molecule\ORM;
use Molecule\ORMFactory;

class HandlerFactory implements HandlerFactoryInterface
{
    /** @var ORMFactory */
    private $factory;

    /** @var RememberInterface */
    private $cache;

    /** @var string */
    private $hash;

    /** @var array */
    private $factories;

    public function __construct(ORMFactory $factory, RememberInterface $cache)
    {
        $this->factory = $factory; $this->cache = $cache; $this->hash = 'Cache_Handler_'.md5(get_class($factory));
    }

    public function handler(string $name, string $handler): HandlerFactoryInterface
    {
        $this->factories[$name] = new $handler($this->factory->table($name), $this->hash); return $this;
    }

    public function unpack(string $name): ?array
    {
        if(!array_key_exists($name, $this->factories)) return null; $handler = $this->factories[$name];

        return $this->cache->remember(...$handler->conditions());
    }

    public function table(string $name): ORM
    {
        return $this->factory->table($name);
    }

    public function manager(array $handlers): HandlerFactoryInterface
    {
        foreach($handlers as $name => $value) if(!array_key_exists($name, $this->factories)) $this->factories[$name] = new $value($this->factory->table($name), $this->hash); return $this;
    }
}
