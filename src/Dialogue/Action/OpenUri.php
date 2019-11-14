<?php
namespace Dialogue\Action;

use InvalidArgumentException;

class OpenUri extends AbstractAction
{
    protected static $supportedOsTypes = array('default', 'iOS', 'android', 'windows');

    public function __construct($targets, $name = 'Open Link') {
        parent::__construct('OpenUri', $name);
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->properties['targets'] = $this->formatTargets($targets);
    }

    protected function formatTargets(array $targets)
    {
        $formatted = array();
        foreach ($targets as $target) {
            // If the target is an array, validate os type and uri
            if (is_array($target)) {
                if (!isset($target['os']) || !isset($target['uri'])) {
                    throw new InvalidArgumentException("Targets must include 'os' and 'uri' array keys");
                }
                else if (!in_array($target['os'], self::$supportedOsTypes)) {
                    throw new InvalidArgumentException('Valid os types are ' . implode(',', self::$supportedOsTypes));
                }
                else if (!is_string($target['uri'])) {
                    throw new InvalidArgumentException('Target uris must be strings');
                }
                else {
                    $formatted[] = array('os' => $target['os'], 'uri' => $target['uri']);
                }
            }
            elseif (is_string($target)) {
                $formatted[] = array('os' => 'default', 'uri' => $target);
            }
            else {
                throw new InvalidArgumentException('Targets must be strings or arrays');
            }
        }
        return $formatted;
    }
}
