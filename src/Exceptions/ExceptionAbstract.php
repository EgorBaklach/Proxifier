<?php namespace Proxifier\Exceptions;

use LogicException;

/**
 * Class ProxifierException
 * @package ProxifierException\Exceptions
 * @property string $url
 * @property array|null $queries
 * @property array|null $headers
 * @property array|null $proxy
 * @property array|null $options
 */
abstract class ExceptionAbstract extends LogicException
{
    /** @var array */
    private $attr;

    /** @var mixed */
    protected $data;

    public function __construct($message, $status, $data, ...$attributes)
    {
        parent::__construct($message, $status); [$this->attr['url'], $this->attr['queries'], $this->attr['headers'], $this->attr['proxy'], $this->attr['options']] = $attributes; $this->data = $data;
    }

    public function attr(): array
    {
        return [$this->url, null, $this->headers, null, $this->data, $this->options];
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
