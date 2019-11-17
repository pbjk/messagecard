<?php

namespace Dialogue\MessageCard\Input;

class DateInput extends AbstractInput
{

    protected $includeTime;

    public function __construct($title, $id = null)
    {
        parent::__construct('DateInput', $title, $id);
    }

    public function includeTime($includeTime = true)
    {
        $this->includeTime = $includeTime;
        return $this;
    }
}
