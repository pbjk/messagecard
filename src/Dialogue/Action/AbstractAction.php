<?php

namespace Dialogue\Action;

abstract class AbstractAction
{
    protected $properties;

    public function __construct($type, $name) {
        $this->properties['@type'] = $type;
        $this->properties['name'] = $name;
    }

    public function getProperties() {
        return $this->properties;
    }
}
