<?php

namespace MessageCard\Processor;

use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

class RepoUriProcessor extends AbstractPlaceholderProcessor
{
    protected $remote, $local, $level;
    const KEY = 'repo_uri';

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
        $record['extra'][self::KEY] = "{$this->remote}/{$file}#L{$line}"; // TODO: This DOES overwrite...
        return $record;
    }

    public function getPlaceholder()
    {
        return '{{' . self::KEY . '}}';
    }
}
