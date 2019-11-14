<?php

namespace Dialogue\Input;

class DateInput extends AbstractInput
{
    public function __construct($title, $id = null)
    {
        parent::__construct('DateInput', $title, $id);
    }

    public function includeTime($includeTime = true)
    {
        $this->properties['includeTime'] = $includeTime;
        return $this;
    }
}
