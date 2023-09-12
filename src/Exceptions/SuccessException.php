<?php namespace Proxifier\Exceptions;

class SuccessException extends ExceptionAbstract implements SuccessInterface
{
    public function __construct(...$attributes)
    {
        parent::__construct('Ok', static::code, ...$attributes);
    }
}