<?php

namespace MessageCard\Processor;

use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class RepoUriProcessor extends AbstractPlaceholderProcessor
{
    protected $remote;
    protected $local;
    protected $level;
    protected $overwrite = false;
    protected $key = 'repo_uri';

    public function __construct($remote, $local, $level = Logger::CRITICAL)
    {
        $this->remote = rtrim($remote, '/');
        $this->local = rtrim($local, '/') . '/';
        $this->level = Logger::toMonologLevel($level);
    }

    public function __invoke(array $record)
    {
        if ($record['level'] < $this->level) {
            return $record;
        }

        // No sense in reinventing the wheel; use IntrospectionProcessor to get
        // the file and line number
        $introspect = new IntrospectionProcessor($this->level);
        // Call IntrospectionProcessor::__invoke
        $trace = $introspect($record);

        // Remove local prefix from the local file path
        $file = isset($trace['extra']['file']) ? str_replace($this->local, '', $trace['extra']['file']) : '';
        $line = isset($trace['extra']['line']) ? $trace['extra']['line'] : 0;

        if (!isset($record['extra'][$this->key]) || $this->overwrite) {
            $record['extra'][$this->key] = "{$this->remote}/{$file}#L{$line}";
        }

        return $record;
    }

    public function overwrite($overwrite = true)
    {
        $this->overwrite = ($overwrite === true);
        return $this;
    }
}
