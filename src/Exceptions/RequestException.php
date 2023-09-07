<?php namespace Proxifier\Exceptions;

use Proxifier\Exceptions\Traits\HeaderAndProxy;

class RequestException extends ExceptionAbstract implements RequestInterface
{
    use HeaderAndProxy;

    public function __construct($data, $attributes)
    {
        parent::__construct('Try again', static::code, $data, $attributes);
    }
}