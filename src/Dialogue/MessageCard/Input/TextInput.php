<?php

namespace Dialogue\MessageCard\Input;

class TextInput extends AbstractInput
{

    protected $isMultiline;
    protected $maxLength;

    public function __construct($title, $id = null)
    {
        parent::__construct('TextInput', $title, $id);
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
