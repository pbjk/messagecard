<?php

namespace Dialogue;

use Dialogue\MessageCard\Fact;
use Dialogue\MessageCard\MessageCard;
use Dialogue\MessageCard\Section;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use RuntimeException;

class TeamsHandler extends AbstractProcessingHandler
{

    protected $url;
    protected $card;
    protected $monospace = false;

    /**
     * Create a TeamsHandler
     *
     * @param string $url   Incoming webhook
     * @param int $level
     * @param string $title
     * @param bool $bubble
     * @return Self
     */
    public function __construct($url, $level = Logger::CRITICAL, $title = null, $bubble = true)
    {
        $this->url = $url;

        $title = is_null($title) ? "New Monolog message from " . gethostname() : $title;
        $this->card = new MessageCard($title);

        parent::__construct($level, $bubble);
    }

    public function getCard()
    {
        return $this->card;
    }

    /**
     * A section has a title, subtitle, image, and list of 'facts' (sort of key
     * value pairs); we only need one section for a log entry
     *
     * @param array $record
     * @return Section
     */
    protected function generateDefaultSection(array $record)
    {
        $section = new Section();
        $section->activityTitle = $record['message'];
        $section->activitySubtitle = $record['datetime']->format('Y-m-d H:i:s');
        $section->facts = Fact::makeFromArrays(
            array('level' => $record['level_name']),
            $record['context'],
            $record['extra'],
        );
        return $section;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        // If user didn't specify anything, then generate a default card
        if (empty($this->card->sections)) {
            $this->card->pushSection($this->generateDefaultSection($record));
        }
        $this->card->send($this->url, $record['extra']);
    }
}
