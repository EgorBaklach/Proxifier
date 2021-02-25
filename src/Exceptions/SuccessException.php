<?php namespace Proxifier\Exceptions;

class SuccessException extends ExceptionAbstract
{
    public function __construct($data, $attributes)
    {
        parent::__construct(false, $data, ...$attributes);
    }
}