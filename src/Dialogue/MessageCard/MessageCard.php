<?php

namespace Dialogue\MessageCard;

use Dialogue\MessageCard\AbstractMessageCardEntity;
use Dialogue\MessageCard\Action\AbstractAction;

class MessageCard extends AbstractMessageCardEntity
{

    protected $type = 'MessageCard';
    protected $context = 'https://schema.org/extensions';
    public $title;
    public $summary;
    public $correlationId;
    public $themeColor;
    public $text;
    public $sections;
    public $potentialAction;

    public function __construct($title)
    {
        $this->title = $title;
    }

    // Setters are provided in case you like method chaining :)
    public static function new($title)
    {
        return new Self($title);
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function setThemeColor($themeColor)
    {
        $this->themeColor = $themeColor;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function pushSection(Section $section)
    {
        $this->sections[] = $section;
        return $this;
    }

    public function pushAction(AbstractAction $action)
    {
        $this->potentialAction[] = $action;
        return $this;
    }
}
