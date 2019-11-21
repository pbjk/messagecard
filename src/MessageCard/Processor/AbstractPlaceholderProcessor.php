<?php

namespace MessageCard\Processor;

use Monolog\Processor\ProcessorInterface;

abstract class AbstractPlaceholderProcessor implements ProcessorInterface
{
    protected $key = 'placeholder';

    abstract public function __invoke(array $record);

    public function getPlaceholder()
    {
        return '{{' . $this->key . '}}';
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
}
