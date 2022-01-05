<?php

namespace MessageCard\Input;

class MultichoiceInput extends AbstractInput
{
    /**
     * Either "normal" or "expanded". Defaults to "normal"; "expanded" attempts
     * to display all options on a single screen, usually using radio buttons.
     *
     * @var string
     */
    public $style;

    /**
     * Display (name)/value pairs for each choice. Array format:
     * ["Name of Choice" => "value_of_choice"]
     *
     * @var array
     */
    public $choices;

    /**
     * Whether multiple options can be selected at once
     *
     * @var bool
     */
    public $isMultiSelect;

    public function __construct(string $title = 'Select', array $choices = array(), ?string $id = null)
    {
        $this->buildChoices($choices);
        parent::__construct('MultichoiceInput', $title, $id);
    }

    public static function create($title, array $choices = array(), $id = null)
    {
        return new self($title, $choices, $id);
    }

    public function setMultiSelect(bool $isMultiSelect = true)
    {
        $this->isMultiSelect = $isMultiSelect;
        return $this;
    }

    public function setExpanded(bool $expanded = true)
    {
        if ($expanded !== false) {
            $this->style = 'expanded';
        } else {
            $this->style = 'normal';
        }
        return $this;
    }

    protected function buildChoices(array $choices): void
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
