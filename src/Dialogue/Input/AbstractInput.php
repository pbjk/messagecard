<?php

namespace Dialogue\Input;

abstract class AbstractInput
{
    protected $properties;

    public function __construct($type, $title, $id = null)
    {
        $this->properties['@type'] = $type;
        $this->properties['title'] = $title;
        if (!is_null($id)) {
            $this->properties['id'] = $id;
        }
    }

    public function setTitle($title) {
        $this->properties['title'] = $title;
        return $this;
    }

    public function setId($id) {
        $this->properties['id'] = $id;
        return $this;
    }

    public function setValue($value) {
        $this->properties['value'] = $value;
        return $this;
    }

    public function required($isRequired = true) {
        $this->properties['isRequired'] = $isRequired;
        return $this;
    }

    public function getProperties() {
        return $this->properties;
    }
}
