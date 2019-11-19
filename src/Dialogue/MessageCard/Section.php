<?php

namespace Dialogue\MessageCard;

use Dialogue\MessageCard\AbstractMessageCardEntity;
use Dialogue\MessageCard\Action\AbstractAction;
use Dialogue\MessageCard\Fact;

class Section extends AbstractMessageCardEntity
{

    public $title;
    public $text;
    public $startGroup;
    public $activityTitle;
    public $activitySubtitle;
    public $activityText;
    public $facts;
    public $activityImage;
    public $heroImage;
    public $images;
    public $potentialAction;

    // Setters are provided in case you like method chaining :)
    public static function new()
    {
        return new Self();
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function startGroup($startGroup = true)
    {
        $this->startGroup = $startGroup;
        return $this;
    }

    public function setActivityTitle($activityTitle)
    {
        $this->activityTitle = $activityTitle;
        return $this;
    }

    public function setActivitySubtitle($activitySubtitle)
    {
        $this->activitySubtitle = $activitySubtitle;
        return $this;
    }

    public function setActivityText($activityText)
    {
        $this->activityText = $activityText;
        return $this;
    }

    public function setFacts(array $facts)
    {
        $this->facts = Fact::makeFromArrays($facts);
        return $this;
    }

    public function setActivityImage($image_path)
    {
        $this->activityImage = $image_path;
        return $this;
    }

    protected function addImage($image_path, $title)
    {
        return array(
            'image' => $image_path,
            'title' => $title,
        );
    }

    // TODO: This seems to have no effect in Teams messages
    public function setHeroImage($image_path, $title = '')
    {
        $this->heroImage = $this->addImage($image_path, $title);
        return $this;
    }

    public function pushImage($image_path, $title = '')
    {
        $this->images[] = $this->addImage($image_path, $title);
        return $this;
    }

    public function pushAction(AbstractAction $action)
    {
        $this->potentialAction[] = $action;
        return $this;
    }
}
