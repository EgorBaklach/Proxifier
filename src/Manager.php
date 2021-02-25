<?php namespace Proxifier;

use Contracts\Cache\RememberInterface;
use Proxifier\Exceptions\{CounterException, ExceptionAbstract, NotFoundException, RequestException, SuccessException};
use Proxifier\DBHandlers\{Proxies, Agents};
use Proxifier\Controllers\ControllerAbstract;
use Proxifier\Sources\SourceInterface;
use Molecule\ORMFactory;
use Exception;
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
    /** @var RememberInterface */
    private $cache;

    /** @var SplQueue */
    private $queue;

    /** @var ORMFactory */
    private $tables;

    /** @var array */
    private $data;

    public function __construct(ORMFactory $tables, RememberInterface $cache)
    {
        $this->queue = new SplQueue();
        $this->tables = $tables;
        $this->cache = $cache;

        foreach([Proxies::class, Agents::class] as $handler)
        {
            if(!method_exists($this->tables, $handler::table)) continue;

            $hash = implode('.', ['Cache', 'Proxifier', 'Table', ucfirst($handler::table)]);

            $this->data[$handler::table] = $this->cache->remember($hash, 86400, function () use ($handler)
            {
                return call_user_func(new $handler($this->tables->{$handler::table}));
            });
        }
    }

    /**
     * @param $name
     * @return array
     * @throws Exception
     */
    public function __get($name): array
    {
        if(empty($this->data[$name]))
        {
            throw new Exception('DBHandler '.$name.' is not available');
        }

        return $this->data[$name];
    }

    public function enqueue(...$data): self
    {
        $this->queue->enqueue($data);

        return $this;
    }

    public function proxy()
    {
        $proxy = $this->proxies[mt_rand(0, count($this->proxies) - 1)];

        if(method_exists($this->tables, 'proxies'))
        {
            $this->tables->proxies()
                ->where(['id=' => $proxy['id']])
                ->bind(':p', 1, PDO::PARAM_INT)
                ->update(['processes=processes+:p'])
                ->exec();
        }

        return $proxy;
    }

    public function agent($type = 'desktop')
    {
        $agents = $this->agents[$type ?: 'mobile'];

        return $agents[mt_rand(0, count($agents) - 1)]['name'];
    }

    public function headers($type = false, $lang = 'en', array $cookies = []): array
    {
        $headers = ['user-agent' => $this->agent($type)];

        if(!empty($cookies))
        {
            $headers['cookie'] = urldecode(http_build_query($cookies, false, '; '));
        }

        if(in_array($lang, ['ru', 'en']))
        {
            $headers['accept-language'] = $lang;
        }

        return $headers;
    }

    public function init(SourceInterface $source, ControllerAbstract $controller)
    {
        if($this->queue->isEmpty()) return null;

        $source->start();

        while(!$this->queue->isEmpty())
        {
            [$url, $queries, $headers, $proxy, $data, $options] = $this->queue->dequeue();

            $source->set($url, $queries, $headers ?? $this->headers(), $proxy ?? $this->proxy(), $data, $options);
        }

        $source->exec($controller, function(ExceptionAbstract $e)
        {
            if($e instanceof RequestException)
            {
                $this->enqueue(...$e->attr());
            }

            if(!empty($e->proxy['id']) && method_exists($this->tables, 'proxies'))
            {
                $query = $this->tables->proxies()->where(['id=' => $e->proxy['id']])->bind(':process', 1, PDO::PARAM_INT);
                $update = ['processes=processes-:process', 'last_request' => date('Y-m-d H:i:s')];

                switch(true)
                {
                    case $e instanceof CounterException:
                    case $e instanceof RequestException:
                        $query->bind(':block', 1, PDO::PARAM_INT);
                        $update[] = 'blocked=blocked+:block';
                        break;
                    case $e instanceof NotFoundException:
                        $query->bind(':inactive', 1, PDO::PARAM_INT);
                        $update[] = 'inactives=inactives+:inactive';
                        break;
                    case $e instanceof SuccessException:
                        $query->bind(':request', 1, PDO::PARAM_INT);
                        $update[] = 'requests=requests+:request';
                        break;
                }

                $query->update($update)->exec();
            }
        });

        if(!$this->queue->isEmpty())
        {
            $this->init($source, $controller);
        }
    }
}
