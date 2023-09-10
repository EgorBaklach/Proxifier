<?php namespace Proxifier\Exceptions\Traits;

Trait HeaderAndProxy
{
    /** @var array */
    private $additions;

    public function additions(array $additions): self
    {
        $this->additions = $additions; return $this;
    }

    public function attr(): array
    {
        return [$this->url, null, $this->additions['headers'] ?? $this->headers ?? false, $this->additions['proxy'] ?? $this->proxy ?? false, $this->data, $this->options];
    }
}