<?php namespace Proxifier\Exceptions;

class RequestException extends ExceptionAbstract
{
    public function __construct($data, $attributes)
    {
        parent::__construct('RequestException dont caught. Try again.', $data, ...$attributes);
    }
}