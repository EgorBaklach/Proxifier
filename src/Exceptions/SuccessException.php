<?php namespace Proxifier\Exceptions;

class SuccessException extends ExceptionAbstract implements SuccessInterface
{
    public function __construct($data, $attributes)
    {
        parent::__construct('Ok', static::code, $data, $attributes);
    }
}