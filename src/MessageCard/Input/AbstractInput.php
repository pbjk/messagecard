<?php

namespace MessageCard\Input;

use MessageCard\AbstractMessageCardEntity;

abstract class AbstractInput extends AbstractMessageCardEntity
{
    /**
     * Input type, e.g. "TextInput", "DateInput", or "MultichoiceInput"
     *
     * @var string
     */
    protected $type;

    /**
     * Label for input
     *
     * @var string title
     */
    public $title;

    /**
     * An input's ID may be referenced in the URL or body of HttpPost action
     *
     * @var string
     */
    public $id;

    /**
     * Initial value of the input
     *
     * @var string
     */
    public $value;

    /**
     * Whether a value must be provided before an action may be taken
     *
     * @var bool
     */
    public $isRequired;

    public function __construct(string $type, string $title, ?string $id = null)
    {
        $this->type = $type;
        $this->title = $title;
        if (!is_null($id)) {
            $this->id = $id;
        }
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
        return $this;
    }

    public function setRequired(bool $isRequired = true)
    {
        $this->isRequired = ($isRequired !== false);
        return $this;
    }
}
