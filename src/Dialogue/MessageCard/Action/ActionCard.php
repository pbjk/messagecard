<?php

namespace Dialogue\MessageCard\Action;

use Dialogue\MessageCard\Input\AbstractInput;
use Dialogue\MessageCard\Action\AbstractAction;

class ActionCard extends AbstractAction
{

    protected $inputs;
    protected $actions;

    public function __construct($name)
    {
        parent::__construct('ActionCard', $name);
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
