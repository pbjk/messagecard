<?php

namespace Dialogue;

use Dialogue\Action\AbstractAction;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use RuntimeException;

class TeamsHandler extends AbstractProcessingHandler
{

    protected $url;
    protected $title;
    protected $potentialActions;
    protected $monospace = false;
    protected $backtrace = false;

    /**
     * Create a TeamsHandler
     *
     * Currently the message title is static since log messages can be too long
     * for a helpful title, but I was not very confident about this decision
     *
     * @param string $url   Incoming webhook
     * @param int $level
     * @param string $title
     * @param bool $bubble
     * @return Self
     */
    public function __construct($url, $level = Logger::CRITICAL, $title = null, $bubble = true) {
        if (!extension_loaded('curl')) {
            throw new RuntimeException('The curl PHP extension is required to use ' . __CLASS__);
        }

        $this->url = $url;
        $this->title = is_null($title) ? "New Monolog message from " . gethostname() : $title;

        parent::__construct($level, $bubble);
    }

    public function backtrace($enable = true)
    {
        $this->backtrace = ($enable !== false);
        return $this;
    }

    protected function generateBacktraceSections() {
        //$skip_classes = array(__CLASS__, 'Monolog\\');
        $skip_classes = array();
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $sections = array_filter($trace, function ($call) use ($skip_classes) {
            $class = isset($call['class']) ? $call['class'] : '';
            foreach ($skip_classes as $skip_class) {
                if (strpos($class, $skip_class) === 0) {
                    return false;
                }
            }
            return true;
        });

        $formatted_sections = array();

        foreach ($sections as $section) {
            $file = isset($section['file']) ? $section['file'] : 'Unknown file';
            $file = isset($section['line']) ? $file . ":{$section['line']}" : $file;
            unset($section['type']);

            $formatted_section = array(
                'activityTitle' => $file,
                'facts' => $this->generateFacts($section),
            );

            if (empty($formatted_sections)) {
                $formatted_section['startGroup'] = true;
                $formatted_section['title'] = 'Backtrace';
            }

            $formatted_sections[] = $formatted_section;
        }

        return $formatted_sections;
    }

    public function monospace($enable = true)
    {
        $this->monospace = ($enable !== false);
        return $this;
    }

    protected function formatMonospace(array $section)
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return '`' . $value . '`';
            }
            elseif (is_array($value)) {
                return $this->formatMonospace($value);
            }
            else {
                return $value;
            }
        }, $section);
    }

    /**
     * Add PotentialActions to the handler
     *
     * @param Action\AbstractAction
     * @return Self
     */
    public function pushAction(AbstractAction $action)
    {
        $this->potentialActions[] = $action;
        return $this;
    }

    /**
     * Search through $this->potentialActions for any patterns to replace with
     * values from $record['extra']. For example, if you want to include a
     * button in the MessageCard that links to the file that threw an error in
     * the source repository:
     *
     * $teams = new TeamsHandler(...);
     * $teams->pushProcessor(function ($record) {
     *     $record['extra']['repo_uri'] = 'https://github.com/user/repo/blob/master';
     *     $record['extra']['relative_file'] = str_replace('/var/www/html/repos/', '', $record['extra']['file']);
     * });
     * // Use a Monolog IntrospectionProcessor to provide the path to the file that logged this message
     * $teams->pushProcessor(new IntrospectionProcessor());
     * $teams->pushAction(new OpenUri('{{repo_uri}}/{{relative_file}}'));
     *
     * @param array $record
     * @return array
     */
    protected function generatePotentialActions(array $record)
    {
        $formatted = array();
        foreach ($this->potentialActions as $action) {
            $action = $action->getProperties();
            array_walk_recursive($action, function(&$value, $key) use ($record) {
                $value = preg_replace_callback('/{{([a-zA-Z0-9_]+)}}/', function($matches) use ($record) {
                    return isset($record['extra'][$matches[1]]) ? $record['extra'][$matches[1]] : $matches[0];
                }, $value);
            });
            $formatted[] = $action;
        }
        return $formatted;
    }

    /**
     * The MessageCard format requires that 'facts' arrays be formatted as follows:
     * Array:               [ 'concurrent_users' => 999 ]
     * MessageCard JSON:    { "name": "environment", "value": "999" }
     *
     * In the JSON, values must always be strings.
     *
     * @param array $context
     * @return array
     */
    protected function generateFacts(array $facts)
    {
        $formatted_facts = array();
        foreach ($facts as $name => $value) {
            $formatted_facts[] = array(
                'name' => empty($name) ? "None" : (string) $name,
                'value' => empty($value) ? "None" : (string) $value,
            );
        }
        return $formatted_facts;
    }

    /**
     * A section has a title, subtitle, image, and list of 'facts' (sort of key
     * value pairs); we only need one section for a log entry
     *
     * @param array $record
     * @return array
     */
    protected function generateSections(array $record)
    {
        $sections = array(
            array(
                'activityTitle' => $record['message'],
                'activitySubtitle' => $record['datetime']->format('Y-m-d H:i:s'),
                'facts' => array_merge(
                    array(array('name' => 'level', 'value' => $record['level_name'])),
                    $this->generateFacts($record['context']),
                    $this->generateFacts($record['extra']),
                ),
            ),
        );

        if ($this->backtrace) {
            $sections = array_merge($sections, $this->generateBacktraceSections());
        }

        if ($this->monospace) {
            $sections = $this->formatMonospace($sections);
        }

        return $sections;
    }

    /**
     * Set some required properties on the message card that don't change with
     * each record
     *
     * @return array
     */
    protected function initializeMessageCard()
    {
        return array(
            '@type' => 'MessageCard',
            '@context' => 'https://schema.org/extensions',
            'summary' => $this->title,
            'title' => $this->title,
        );
    }

    /**
     * Create MessageCard JSON that will be sent to an incoming webhook
     *
     * @param array $record
     * @return string
     */
    protected function generateMessageCard(array $record)
    {
        $messageCard = $this->initializeMessageCard();
        $messageCard['sections'] = $this->generateSections($record);
        $messageCard['potentialAction'] = $this->generatePotentialActions($record);
        // PHP 5.3 does not have JSON_UNESCAPED_SLASHES, so unescape them manually
        return str_replace('\\/', '/', json_encode($messageCard, JSON_PRETTY_PRINT));
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $curl = curl_init($this->url);
        if ($curl === false) {
            throw new RuntimeException('Unexpected error initializing cURL');
        }

        curl_setopt_array($curl, array(
            CURLOPT_POSTFIELDS => $this->generateMessageCard($record),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        echo $this->generateMessageCard($record);
        // curl_exec($curl);
        curl_close($curl);
    }
}
