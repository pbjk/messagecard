<?php

namespace Dialogue\Processor;

use Monolog\Processor\ProcessorInterface;

abstract class AbstractPlaceholderProcessor implements ProcessorInterface
{
    abstract public function __invoke(array $record);
    abstract public function getPlaceholder();
}
