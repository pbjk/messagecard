<?php

namespace MessageCard\Action;

use MessageCard\Input\AbstractInput;
use MessageCard\Action\AbstractAction;

class ActionCard extends AbstractAction
{
    /**
     * Form fields in the ActionCard.
     *
     * @var AbstractInput[] $inputs
     */
    public $inputs;

    /**
     * Actions that can be performed from the ActionCard. Cannot contain another
     * ActionCard.
     *
     * @var AbstractAction[] $actions
     */
    public $actions;

    public function __construct(string $name)
    {
        parent::__construct('ActionCard', $name);
    }

    public static function create(string $name)
    {
        return new self($name);
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
