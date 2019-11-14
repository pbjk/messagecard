<?php

namespace Dialogue\Input;

class TextInput extends AbstractInput
{
    public function __construct($title, $id = null)
    {
        parent::__construct('TextInput', $title, $id);
    }

    public function multiline($isMultiline = true)
    {
        $this->properties['isMultiline'] = $isMultiline;
        return $this;
    }

    public function setMaxLength($maxLength)
    {
        $this->properties['maxLength'] = $maxLength;
        return $this;
    }
}
