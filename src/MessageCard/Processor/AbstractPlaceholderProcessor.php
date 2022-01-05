<?php

namespace MessageCard\Processor;

use Monolog\Processor\ProcessorInterface;

abstract class AbstractPlaceholderProcessor implements ProcessorInterface
{
    // ProcessorInterface method
    abstract public function __invoke(array $record);

    abstract public function getKey(): string;
    public function getPlaceholder(): string
    {
        return '{{' . $this->getKey() . '}}';
    }
}
