<?php namespace Proxifier\Exceptions\Traits;

Trait HeaderAndProxy
{
    public function additions(array $additions): self
    {
        [$this->attr['headers'], $this->attr['proxy']] = $additions; return $this;
    }
}