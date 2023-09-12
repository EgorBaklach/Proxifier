<?php namespace Proxifier\Exceptions;

use LogicException;

/**
 * Class ProxifierException
 * @package ProxifierException\Exceptions
 * @property string $url
 * @property array|null $queries
 * @property array|null $headers
 * @property array|null $proxy
 * @property array|null $data
 * @property array|null $options
 */
abstract class ExceptionAbstract extends LogicException
{
    /** @var array */
    protected $attr;

    public function __construct($message, $status, ...$attributes)
    {
        [$this->attr['url'], $this->attr['queries'], $this->attr['headers'], $this->attr['proxy'], $this->attr['data'], $this->attr['options']] = $attributes; parent::__construct($message, $status);
    }

    public function attr(): array
    {
        return [$this->url, $this->queries, $this->headers, $this->proxy, $this->data, $this->options];
    }

    public function __get($name)
    {
        return $this->attr[$name] ?: null;
    }

    public function __isset($name): bool
    {
        return isset($this->attr[$name]);
    }
}
