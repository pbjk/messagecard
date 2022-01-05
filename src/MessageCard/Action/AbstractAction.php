<?php

namespace MessageCard\Action;

use MessageCard\AbstractMessageCardEntity;

abstract class AbstractAction extends AbstractMessageCardEntity
{
    /**
     * Action type, e.g. "OpenUri", "HttpPOST", or "ActionCard"
     *
     * @var string $type
     */
    protected $type;

    /**
     * Label for action element
     *
     * @var string $name
     */
    public $name;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
}
