<?php

namespace MessageCard\Input;

class DateInput extends AbstractInput
{
    /**
     * @var bool
     */
    public $includeTime;

    public function __construct(string $title, ?string $id = null)
    {
        parent::__construct('DateInput', $title, $id);
    }

    public static function create(string $title, ?string $id = null)
    {
        return new self($title, $id);
    }

    public function includeTime(bool $includeTime = true)
    {
        $this->includeTime = $includeTime;
        return $this;
    }
}
