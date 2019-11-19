<?php

namespace MessageCard\Input;

use MessageCard\AbstractMessageCardEntity;

abstract class AbstractInput extends AbstractMessageCardEntity
{
    protected $type;
    public $title;
    public $id;
    public $value;
    public $isRequired;

    public function __construct($type, $title, $id = null)
    {
        $this->type = $type;
        $this->title = $title;
        if (!is_null($id)) {
            $this->id = $id;
        }
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function required($isRequired = true)
    {
        $this->isRequired = ($isRequired !== false);
        return $this;
    }
}
