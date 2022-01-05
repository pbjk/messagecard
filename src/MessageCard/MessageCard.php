<?php

namespace MessageCard;

use MessageCard\Action\AbstractAction;
use RuntimeException;

class MessageCard extends AbstractMessageCardEntity
{
    /**
     * Type: required, static value
     *
     * @var string
     */
    protected $type = 'MessageCard';

    /**
     * Context: required, static value
     *
     * @var string
     */
    protected $context = 'https://schema.org/extensions';

    /**
     * Title, rendered at the top of the card
     *
     * @var string
     */
    public $title;

    /**
     * Short description of card content. Required if $text is unset.
     *
     * @var string
     */
    public $summary;

    /**
     * UUID that will be sent in requests invoked by card actions
     *
     * @var string
     */
    public $correlationId;

    /**
     * RRGGBB hex color. No leading `#`.
     *
     * @var string
     */
    public $themeColor;

    /**
     * Main content of the card. Required if $summary is unset.
     *
     * @var string
     */
    public $text;

    /**
     * List of sections to include in the card
     *
     * @var Section[]
     */
    public $sections;

    /**
     * List of actions that can be invoked on the card
     *
     * @var AbstractAction[]
     */
    public $potentialAction;

    public function __construct(string $title, ?string $summary = null)
    {
        $this->title = $title;
        $this->summary = is_null($summary) ? $title : $summary;
    }

    /**
     * Send the MessageCard to incoming webhook
     *
     * @param string $url
     * @param array $placeholders
     * @return void
     */
    public function send(string $url, array $placeholders = array()): void
    {
        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('Unexpected error initializing curl');
        }

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $this->toJson($placeholders),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        if (curl_exec($curl) === false) {
            throw new RuntimeException(
                "Error sending MessageCard to incoming webhook (code: "
                    . curl_errno($curl)
                    . "; message: '"
                    . curl_error($curl)
                    . "')"
            );
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code < 200 || $http_code > 299) {
            throw new RuntimeException("Non-successful response code ($http_code) returned from '{$url}'");
        }

        curl_close($curl);
    }

    /**
     * Replace placeholders (if any) with content.
     * $placeholders array format: ["placeholder_key" => "Placeholder Value"]
     *
     * @param array $placeholders
     * @return string
     */
    public function toJson(array $placeholders = array()): string
    {
        $card_as_string = json_encode($this, JSON_UNESCAPED_SLASHES);

        if (!empty($placeholders)) {
            $card_as_string = preg_replace_callback('/{{([a-zA-Z0-9_]+)}}/', function ($matches) use ($placeholders) {
                return isset($placeholders[$matches[1]]) ? $placeholders[$matches[1]] : $matches[0];
            }, $card_as_string);
        }

        return $card_as_string;
    }

    // Setters are provided in case you like method chaining :)
    public static function create(string $title, ?string $section = null)
    {
        return new self($title, $section);
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setSummary(string $summary)
    {
        $this->summary = $summary;
        return $this;
    }

    public function setCorrelationId(string $correlationId)
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function setThemeColor(string $themeColor)
    {
        $this->themeColor = $themeColor;
        return $this;
    }

    public function setText(string $text)
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
