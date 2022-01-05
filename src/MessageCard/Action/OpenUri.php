<?php

namespace MessageCard\Action;

use InvalidArgumentException;

class OpenUri extends AbstractAction
{
    /**
     * Specifying the OS type allows for opening the target in an app.
     *
     * @var array
     */
    protected static $supportedOsTypes = ['default', 'iOS', 'android', 'windows'];

    /**
     * Target URIs
     *
     * @var array
     */
    public $targets;

    public function __construct($targets, string $name = 'Open Link')
    {
        parent::__construct('OpenUri', $name);
        $this->setTargets($targets);
    }

    public static function create($targets, string $name = 'Open Link')
    {
        return new self($targets, $name);
    }

    public function setTargets($targets)
    {
        if (!is_array($targets)) {
            $targets = [$targets];
        }
        $this->targets = $this->formatTargets($targets);
        return $this;
    }

    protected function formatTargets(array $targets): array
    {
        $formatted = [];
        foreach ($targets as $target) {
            // If the target is an array, validate os type and uri
            if (is_array($target)) {
                if (!isset($target['os']) || !isset($target['uri'])) {
                    throw new InvalidArgumentException("Targets must include 'os' and 'uri' array keys");
                } elseif (!in_array($target['os'], self::$supportedOsTypes)) {
                    throw new InvalidArgumentException('Valid os types are ' . implode(',', self::$supportedOsTypes));
                } elseif (!is_string($target['uri'])) {
                    throw new InvalidArgumentException('Target uris must be strings');
                } else {
                    $formatted[] = ['os' => $target['os'], 'uri' => $target['uri']];
                }
            } elseif (is_string($target)) {
                $formatted[] = ['os' => 'default', 'uri' => $target];
            } else {
                throw new InvalidArgumentException('Targets must be strings or arrays');
            }
        }
        return $formatted;
    }
}
