<?php
namespace Dialogue\Action;

use InvalidArgumentException;
use Dialogue\Input\AbstractInput;
use Dialogue\Action\AbstractAction;

class ActionCard extends AbstractAction
{
    public function __construct($name)
    {
        parent::__construct('ActionCard', $name);
    }

    public function pushInput(AbstractInput $input)
    {
        $this->properties['inputs'][] = $input->getProperties();
        return $this;
    }

    public function pushAction(AbstractAction $action)
    {
        $this->properties['actions'][] = $action->getProperties();
        return $this;
    }
}
