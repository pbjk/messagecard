<?php

namespace MessageCard\Input;

class TextInput extends AbstractInput
{
    /**
     * Whether multiple lines of text should be accepted
     *
     * @var
     */
    public $isMultiline;

    /**
     * Maximum number of characters that can be entered
     *
     * @var
     */
    public $maxLength;

    public function __construct(string $title, ?string $id = null)
    {
        parent::__construct('TextInput', $title, $id);
    }

    public static function create($title, $id = null)
    {
        return new self($title, $id);
    }

    public function setMultiline(bool $isMultiline = true)
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
