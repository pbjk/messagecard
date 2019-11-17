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
        if (!extension_loaded('curl')) {
            throw new RuntimeException('The curl PHP extension is required to use ' . __CLASS__);
        }

        $this->url = $url;

        $title = is_null($title) ? "New Monolog message from " . gethostname() : $title;
        $this->card = new MessageCard($title);

        parent::__construct($level, $bubble);
    }

    public function getCard()
    {
        return $this->card;
    }

    public function monospace($enable = true)
    {
        $this->monospace = ($enable !== false);
        return $this;
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
     * Search through the card for any patterns to replace with values from $record['extra'].
     *
     * @param array $record
     * @return string
     */
    protected function replacePlaceholders(array $record)
    {
        $card_as_string = json_encode($this->card);
        $card_as_string = preg_replace_callback('/{{([a-zA-Z0-9_]+)}}/', function ($matches) use ($record) {
            return isset($record['extra'][$matches[1]]) ? $record['extra'][$matches[1]] : $matches[0];
        }, $card_as_string);
        // JSON_UNESCAPED_SLASHES was introduced in PHP 5.4, so remove the escaping manually
        return str_replace('\\/', '/', $card_as_string);
    }

    /**
     * Create MessageCard JSON that will be sent to an incoming webhook
     *
     * @param array $record
     * @return string
     */
    protected function generateMessageCard(array $record)
    {
        // If user didn't specify anything, then generate a default card
        if (empty($this->card->sections)) {
            $this->card->pushSection($this->generateDefaultSection($record));
        }

        if ($this->monospace) {
            foreach ($this->card->sections as $section) {
                $section->formatMonospace();
            }
        }

        return $this->replacePlaceholders($record);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $curl = curl_init($this->url);
        if ($curl === false) {
            throw new RuntimeException('Unexpected error initializing curl');
        }

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $this->generateMessageCard($record),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        if (curl_exec($curl) === false) {
            throw new RuntimeException("Error sending MessageCard to incoming webhook (code: " . curl_errno($curl) . "; message: '" . curl_error($curl) . "')");
        }

        $http_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if ($http_code < 200 || $http_code > 299) {
            throw new RuntimeException("Non-successful response code ($http_code) returned from '{$this->url}'");
        }

        curl_close($curl);
    }
}
