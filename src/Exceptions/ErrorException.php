<?php namespace Proxifier\Exceptions;

class ErrorException extends ExceptionAbstract
{
    public function __construct(...$attributes)
    {
        parent::__construct('Error Exception', 503, ...$attributes);
    }
}