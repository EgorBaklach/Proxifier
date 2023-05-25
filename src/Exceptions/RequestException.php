<?php namespace Proxifier\Exceptions;

class RequestException extends ExceptionAbstract implements RequestInterface
{
    public function __construct($data, $attributes)
    {
        parent::__construct('RequestException dont caught. Try again.', static::code, $data, ...$attributes);
    }
}