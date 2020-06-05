<?php

namespace MessageCard;

use InvalidArgumentException;
use Traversable;

class Fact
{
    /**
     * The MessageCard format requires that 'facts' arrays be formatted as follows:
     * Array:               [ 'concurrent_users' => 999 ]
     * MessageCard JSON:    { "name": "environment", "value": "999" }
     *
     * In the JSON, values must always be strings.
     *
     * @return array
     */
    public static function makeFromArrays()
    {
        $formatted_facts = array();
        foreach (func_get_args() as $arg) {
            if (!is_array($arg) && !($arg instanceof Traversable)) {
                throw new InvalidArgumentException('Non-Traversable argument provided');
            }
            foreach ($arg as $name => $value) {
                $formatted_facts[] = array(
                    'name' => empty($name) ? "None" : (string) $name,
                    'value' => empty($value) ? "None" : (string) $value,
                );
            }
        }
        return $formatted_facts;
    }
}
