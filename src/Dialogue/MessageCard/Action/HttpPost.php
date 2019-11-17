<?php

namespace Dialogue\MessageCard\Action;

use InvalidArgumentException;

class HttpPost extends AbstractAction
{

    protected $target;
    protected $body;
    protected $headers;
    protected $bodyContentType;

    public function __construct($target, $name, $body = '')
    {
        parent::__construct('HttpPOST', $name);
        $this->target = $target;
        $this->body = $body;
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
