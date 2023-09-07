<?php namespace Proxifier;

use Proxifier\Exceptions\{ExceptionAbstract, NotFoundInterface, RequestInterface, SuccessInterface};
use Proxifier\Handlers\{Agents, HandlerFactoryInterface, Proxies};
use Proxifier\Sources\SourceInterface;
use SplQueue;
use PDO;

/**
 * Class Manager
 * @package Proxifier
 * @property array $proxies
 * @property array $agents
 */
class Manager
{
    /** @var SplQueue */
    private $queue;

    /** @var SourceInterface */
    private $source;

    /** @var HandlerFactoryInterface */
    private $factory;

    /** @var string */
    private $ptype = 'mobile';

    /** @var string */
    private $utype = 'mobile';

    private const handlers = ['agents' => Agents::class, 'proxies' => Proxies::class];

    public function __construct(SourceInterface $source, HandlerFactoryInterface $factory)
    {
        $this->queue = new SplQueue; $this->source = $source; $this->factory = $factory->manager(self::handlers);
    }

    public function handler(string $name, string $handle): self
    {
        $this->factory->handler($name, $handle); return $this;
    }

    public function enqueue(...$data): self
    {
        $this->queue->enqueue($data); return $this;
    }

    /** SET PROXIES TYPE */
    public function ptype(string $type): self
    {
        $this->ptype = $type; return $this;
    }

    /** SET USER-AGENTS TYPE */
    public function utype(string $type): self
    {
        $this->utype = $type; return $this;
    }

    public function __get($name): ?array
    {
        return $this->factory->unpack($name);
    }

    public function proxy(?string $type = null): array
    {
        $proxies = $this->proxies[$type ?: $this->ptype]; return $proxies[mt_rand(0, count($proxies) - 1)];
    }

    public function agent(?string $type = null): string
    {
        $agents = $this->agents[$type ?: $this->utype]; return $agents[mt_rand(0, count($agents) - 1)]['name'];
    }

    public function headers(?string $type = null): array
    {
        return ['user-agent' => $this->agent($type)];
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }

    public function __clone()
    {
        $this->queue = new SplQueue;
    }

    public function init(callable $controller): void
    {
        if($this->queue->isEmpty()) return; $this->source->start();

        while(!$this->queue->isEmpty())
        {
            [$url, $queries, $headers, $proxy, $data, $options] = $this->queue->dequeue(); if($proxy === null) $proxy = $this->proxy();

            if(!empty($proxy)) $this->factory->table('proxies')->where(['id=' => $proxy['id']])->bind(':p', 1, PDO::PARAM_INT)->update(['processes=processes+:p'])->exec();

            $this->source->set($url, $queries, $headers ?? $this->headers(), $proxy, $data, $options);
        }

        $this->source->exec($controller, function(ExceptionAbstract $e)
        {
            if($e instanceof RequestInterface) $this->enqueue(...$e->attr());

            if(!empty($e->proxy['id']))
            {
                $query = $this->factory->table('proxies')->where(['id=' => $e->proxy['id']])->bind(':process', 1, PDO::PARAM_INT);
                $update = ['processes=processes-:process', 'last_request' => date('Y-m-d H:i:s')];

                switch(true)
                {
                    case $e instanceof NotFoundInterface: $query->bind(':inactive', 1, PDO::PARAM_INT); $update[] = 'inactives=inactives+:inactive'; break;
                    case $e instanceof SuccessInterface: $query->bind(':request', 1, PDO::PARAM_INT); $update[] = 'requests=requests+:request'; break;
                    default: $query->bind(':block', 1, PDO::PARAM_INT); $update[] = 'blocked=blocked+:block';
                }

                $query->update($update)->exec();
            }
        });

        if(!$this->queue->isEmpty()) $this->init($controller);
    }
}
