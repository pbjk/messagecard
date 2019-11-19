<?php

namespace Dialogue\MessageCard\Action;

use InvalidArgumentException;

class HttpPost extends AbstractAction
{

    public $target;
    public $body;
    public $headers;
    public $bodyContentType;

    public function __construct($target, $name, $body = '')
    {
        parent::__construct('HttpPOST', $name);
        $this->target = $target;
        $this->body = $body;
    }

    public static function new($target, $name, $body = '')
    {
        return new Self($target, $name, $body);
    }

    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = array();
        foreach ($headers as $name => $value) {
            if (!is_string($name) || (!is_string($value) && !is_int($value))) {
                throw new InvalidArgumentException(
                    'Headers must be key-value pairs, where keys are strings and values are strings or integers'
                );
            }
            $this->headers[] = array('name' => $name, 'value' => $value);
        }
        return $this;
    }

    public function setContentType($bodyContentType)
    {
        $this->bodyContentType = $bodyContentType;
        return $this;
    }
}
