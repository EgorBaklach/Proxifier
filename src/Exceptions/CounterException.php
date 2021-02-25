<?php namespace Proxifier\Exceptions;

class CounterException extends ExceptionAbstract
{
    public function __construct($data, $attributes)
    {
        parent::__construct('Stop counter invoke', $data, ...$attributes);
    }
}
