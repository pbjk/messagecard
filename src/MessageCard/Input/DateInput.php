<?php

namespace MessageCard\Input;

class DateInput extends AbstractInput
{
    public $includeTime;

    public function __construct($title, $id = null)
    {
        parent::__construct('DateInput', $title, $id);
    }

    public static function create($title, $id = null)
    {
        return new self($title, $id);
    }

    public function includeTime($includeTime = true)
    {
        $this->includeTime = ($includeTime !== false);
        return $this;
    }
}
