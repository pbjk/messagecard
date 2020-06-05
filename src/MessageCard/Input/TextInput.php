<?php

namespace MessageCard\Input;

class TextInput extends AbstractInput
{
    public $isMultiline;
    public $maxLength;

    public function __construct($title, $id = null)
    {
        parent::__construct('TextInput', $title, $id);
    }

    public static function create($title, $id = null)
    {
        return new self($title, $id);
    }

    public function multiline($isMultiline = true)
    {
        $this->isMultiline = $isMultiline;
        return $this;
    }

    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }
}
