<?php

namespace Tests;

use MessageCard\Processor\RepoUriProcessor;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class RepoUriProcessorTest extends TestCase
{
    protected $repo;
    protected $handler;
    protected $logger;

    protected function setUp(): void
    {
        $this->repo = new RepoUriProcessor('https://github.com/user/repo', '/var/www/html', Logger::DEBUG);
        $this->handler = new TestHandler();

        $this->logger = new Logger('name');
        $this->logger->pushHandler($this->handler);
        $this->logger->pushProcessor($this->repo);
    }

    public function testRepoUriProcessorCanGetPlaceholder()
    {
        $this->assertEquals($this->repo->getPlaceholder(), '{{repo_uri}}');
    }

    public function testCannotOverwriteRecordContentsByDefault()
    {
        $this->logger->pushProcessor(function ($record) {
            $record['extra'][$this->repo->getKey()] = 'already_set';
            return $record;
        });
        $this->logger->debug('Test message');
        $record = $this->handler->getRecords();
        $record = array_shift($record);
        $this->assertEquals($record['extra'][$this->repo->getKey()], 'already_set');
    }

    public function testCanOverwriteRecordContents()
    {
        $this->repo->overwrite();

        $this->logger->pushProcessor(function ($record) {
            $record['extra'][$this->repo->getKey()] = 'already_set';
            return $record;
        });
        $this->logger->debug('Test message');
        $record = $this->handler->getRecords();
        $record = array_shift($record);
        $this->assertNotEquals($record['extra'][$this->repo->getKey()], 'already_set');
    }
}
