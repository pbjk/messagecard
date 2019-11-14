<?php

namespace Dialogue\Input;

class MultichoiceInput extends AbstractInput
{
    public function __construct($title, array $choices = array(), $id = null)
    {
        $this->buildChoices($choices);
        parent::__construct('DateInput', $title, $id);
    }

    public function multiSelect($isMultiSelect = true)
    {
        $this->properties['isMultiSelect'] = $isMultiSelect;
        return $this;
    }

    public function expanded($expanded = true)
    {
        if ($expanded) {
            $this->properties['style'] = 'expanded';
        }
        else {
            $this->properties['style'] = 'normal';
        }
        return $this;
    }

    protected function buildChoices(array $choices) {
        foreach ($choices as $display => $value) {
            // If it's not an associative array, use the value for both properties
            if (is_int($display)) {
                $this->properties['choices'][] = array('display' => $value, 'value' => $value);
            }
            else {
                $this->properties['choices'][] = array('display' => $display, 'value' => $value);
            }
        }
    }
}
