<?php

namespace MessageCard\Action;

use MessageCard\Input\AbstractInput;
use MessageCard\Action\AbstractAction;

class ActionCard extends AbstractAction
{

    public $inputs;
    public $actions;

    public function __construct($name)
    {
        parent::__construct('ActionCard', $name);
    }

    public static function create($name)
    {
        return new Self($name);
    }

    public function pushInput(AbstractInput $input)
    {
        $this->inputs[] = $input;
        return $this;
    }

    public function pushAction(AbstractAction $action)
    {
        $this->actions[] = $action;
        return $this;
    }
}
