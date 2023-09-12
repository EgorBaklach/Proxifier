<?php namespace Proxifier\Exceptions;

class NotFoundException extends ExceptionAbstract implements NotFoundInterface
{
    public function __construct(...$attributes)
    {
        parent::__construct('Page is not found', static::code, ...$attributes);
    }
}