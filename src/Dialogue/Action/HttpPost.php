<?php
namespace Dialogue\Action;

use InvalidArgumentException;

class HttpPost extends AbstractAction
{
    public function __construct($target, $name, $body = '')
    {
        parent::__construct('HttpPOST', $name);
        $this->properties['target'] = $target;
        $this->properties['body'] = $body;
    }

    public function setHeaders(array $headers)
    {
        $this->properties['headers'] = array();
        foreach ($headers as $name => $value) {
            if (!is_string($name) || (!is_string($value) && !is_int($value))) {
                throw new InvalidArgumentException(
                    'Headers must be key-value pairs, where keys are strings and values are strings or integers'
                );
            }
            $this->properties['headers'][] = array('name' => $name, 'value' => $value);
        }
        return $this;
    }

    public function setContentType($bodyContentType)
    {
        $this->properties['bodyContentType'] = $bodyContentType;
        return $this;
    }
}
