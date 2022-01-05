<?php

namespace MessageCard\Action;

use InvalidArgumentException;

class HttpPost extends AbstractAction
{
    /**
     * URL of the service that implements the action
     *
     * @var string
     */
    public $target;

    /**
     * Body of the POST request
     *
     * @var string
     */
    public $body;

    /**
     * Headers that will be sent with the POST request
     *
     * @var array
     */
    public $headers;

    /**
     * MIME type of POST body - defaults to application/json
     *
     * @param string
     */
    public $bodyContentType;

    public function __construct(string $target, string $name, string $body = '')
    {
        parent::__construct('HttpPOST', $name);
        $this->target = $target;
        $this->body = $body;
    }

    public static function create(string $target, string $name, string $body = '')
    {
        return new self($target, $name, $body);
    }

    public function setTarget(string $target)
    {
        $this->target = $target;
        return $this;
    }

    public function setBody(string $body)
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

    public function setContentType(string $bodyContentType)
    {
        $this->bodyContentType = $bodyContentType;
        return $this;
    }
}
