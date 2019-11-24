<?php

namespace MessageCard;

use MessageCard\Action\AbstractAction;
use RuntimeException;

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

    public function __construct($title, $summary = null)
    {
        $this->title = $title;
        $this->summary = is_null($summary) ? $title : $summary;
    }

    public function send($url, $placeholders = array())
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
            throw new RuntimeException("Error sending MessageCard to incoming webhook (code: " . curl_errno($curl) . "; message: '" . curl_error($curl) . "')");
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code < 200 || $http_code > 299) {
            throw new RuntimeException("Non-successful response code ($http_code) returned from '{$url}'");
        }

        curl_close($curl);
    }

    public function toJson(array $placeholders = array())
    {
        $card_as_string = json_encode($this);

        if (!empty($placeholders)) {
            $card_as_string = preg_replace_callback('/{{([a-zA-Z0-9_]+)}}/', function ($matches) use ($placeholders) {
                return isset($placeholders[$matches[1]]) ? $placeholders[$matches[1]] : $matches[0];
            }, $card_as_string);
        }

        // JSON_UNESCAPED_SLASHES was introduced in PHP 5.4, so remove the escaping manually
        return str_replace('\\/', '/', $card_as_string);
    }

    public function monospace()
    {
        if (!empty($this->sections)) {
            foreach($this->sections as $section) {
                $section->formatMonospace();
            }
        }

        return $this;
    }

    // Setters are provided in case you like method chaining :)
    public static function new($title, $section = null)
    {
        return new Self($title, $section);
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
