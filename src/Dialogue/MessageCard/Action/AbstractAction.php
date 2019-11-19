<?php

namespace Dialogue\MessageCard\Action;

use Dialogue\MessageCard\AbstractMessageCardEntity;

abstract class AbstractAction extends AbstractMessageCardEntity
{
    protected $type;
    public $name;

    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
