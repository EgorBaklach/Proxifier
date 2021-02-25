<?php namespace Proxifier\Exceptions;

class NotFoundException extends ExceptionAbstract
{
    public function __construct($data, $attributes)
    {
        parent::__construct('Page is not found', $data, ...$attributes);
    }
}