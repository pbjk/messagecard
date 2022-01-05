<?php

namespace MessageCard;

use MessageCard\Action\AbstractAction;

class Section extends AbstractMessageCardEntity
{
    /**
     * Section summary
     *
     * @var string
     */
    public $title;

    /**
     * Section body
     *
     * @var string
     */
    public $text;

    /**
     * Whether this section starts a logical group of card elements
     *
     * @var bool
     */
    public $startGroup;

    /**
     * @var string
     */
    public $activityTitle;

    /**
     * @var string
     */
    public $activitySubtitle;

    /**
     * @var string
     */
    public $activityText;

    /**
     * @var string
     */
    public $activityImage;

    /**
     * Data relating to the section, usually formatted as a list / table.
     * Consists of name/value pairs.
     *
     * @var array
     */
    public $facts;

    /**
     * Large, central image for the section
     *
     * @var array
     */
    public $heroImage;

    /**
     * Photo gallery
     *
     * @var array
     */
    public $images;

    /**
     * @var AbstractAction[]
     */
    public $potentialAction;

    public static function create()
    {
        return new self();
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    public function startGroup(bool $startGroup = true)
    {
        $this->startGroup = $startGroup;
        return $this;
    }

    public function setActivityTitle(string $activityTitle)
    {
        $this->activityTitle = $activityTitle;
        return $this;
    }

    public function setActivitySubtitle(string $activitySubtitle)
    {
        $this->activitySubtitle = $activitySubtitle;
        return $this;
    }

    public function setActivityText(string $activityText)
    {
        $this->activityText = $activityText;
        return $this;
    }

    public function setFacts(array $facts)
    {
        $this->facts = Fact::makeFromArrays($facts);
        return $this;
    }

    public function setActivityImage(string $image_path)
    {
        $this->activityImage = $image_path;
        return $this;
    }

    protected function addImage(string $image_path, string $title)
    {
        return array(
            'image' => $image_path,
            'title' => $title,
        );
    }

    // TODO: This seems to have no effect in Teams messages
    public function setHeroImage(string $image_path, string $title = '')
    {
        $this->heroImage = $this->addImage($image_path, $title);
        return $this;
    }

    public function pushImage(string $image_path, string $title = '')
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
