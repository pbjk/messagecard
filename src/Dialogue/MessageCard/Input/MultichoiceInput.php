<?php

namespace Dialogue\MessageCard\Input;

class MultichoiceInput extends AbstractInput
{

    public $style;
    public $choices;
    public $isMultiSelect;

    public function __construct($title = 'Select', array $choices = array(), $id = null)
    {
        $this->buildChoices($choices);
        parent::__construct('MultichoiceInput', $title, $id);
    }

    public static function new($title, array $choices = array(), $id = null)
    {
        return new Self($title, $choices, $id);
    }

    public function multiSelect($isMultiSelect = true)
    {
        $this->isMultiSelect = ($isMultiSelect !== false);
        return $this;
    }

    public function expanded($expanded = true)
    {
        if ($expanded !== false) {
            $this->style = 'expanded';
        } else {
            $this->style = 'normal';
        }
        return $this;
    }

    protected function buildChoices(array $choices)
    {
        foreach ($choices as $display => $value) {
            // If it's not an associative array, use the value for both properties
            if (is_int($display)) {
                $this->choices[] = array('display' => $value, 'value' => $value);
            } else {
                $this->choices[] = array('display' => $display, 'value' => $value);
            }
        }
    }
}
