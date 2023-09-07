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

    /** @var HandlerInterface[] */
    private $factories;

    public function __construct(ORMFactory $factory, RememberInterface $cache)
    {
        $this->factory = $factory; $this->cache = $cache; $this->hash = 'Cache_Handler_'.md5(get_class($factory));
    }

    public function handler(string $name, string $handler): HandlerFactoryInterface
    {
        $this->factories[$name] = new $handler($this->factory->table($name), $this->hash); return $this;
    }

    public function unpack(string $name): array
    {
        return $this->cache->remember(...$this->factories[$name]->conditions());
    }

    public function table(string $name): ORM
    {
        return $this->factory->table($name);
    }

    public function manager(array $handlers): HandlerFactoryInterface
    {
        foreach($handlers as $name => $handler) if(!array_key_exists($name, $this->factories)) $this->handler($name, $handler); return $this;
    }
}
