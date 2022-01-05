<?php

namespace MessageCard;

use MessageCard\Processor\AbstractPlaceholderProcessor;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class TeamsHandler extends AbstractProcessingHandler
{

    protected $url;
    protected $card;
    protected $excludedFields = array();
    protected $monospace = false;

    /**
     * Create a TeamsHandler
     *
     * @param string $url   Incoming webhook
     * @param int $level
     * @param string $title
     * @param bool $bubble
     * @return self
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
     * Augment parent::pushProcessor with special behavior for
     * AbstractPlaceholderProcessor, so any unwanted placeholders can be removed
     * from the output later
     *
     * @param callable $callback
     */
    public function pushProcessor(callable $callback): HandlerInterface
    {
        if ($callback instanceof AbstractPlaceholderProcessor) {
            $this->excludedFields[] = $callback->getKey();
        }
        parent::pushProcessor($callback);
        return $this;
    }

    /**
     * Get a list of placeholders for processors that have been pushed to this
     * handler
     *
     * @return array
     */
    public function getExcludedFields()
    {
        return $this->excludedFields;
    }

    /**
     * Remove any elements from $record['extra'] whose keys are found in
     * $this->excludedFields
     *
     * @param array $extra
     */
    protected function excludeFields($extra)
    {
        return array_diff_key($extra, array_flip($this->excludedFields));
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
            $this->excludeFields($record['extra']),
        );

        return $section;
    }

    protected function write(array $record): void
    {
        // If user didn't specify anything, then generate a default card
        if (empty($this->card->sections)) {
            $this->card->pushSection($this->generateDefaultSection($record));
        }
        $this->card->send($this->url, $record['extra']);
    }
}
