<?php

namespace Tests;

use DateTime;
use MessageCard\Section;
use MessageCard\TeamsHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TeamsHandlerTest extends TestCase
{
    protected $handler;
    protected $record;
    protected $defaultCard;

    protected function setUp(): void
    {
        $this->handler = new TeamsHandler('uri', Logger::DEBUG, 'title');
        $this->record = [
            'message' => 'An error has occurred',
            'context' =>
            [
                'info' => 'items in the context array may be turned into "facts"',
            ],
            'level' => 100,
            'level_name' => 'DEBUG',
            'channel' => 'channel_name',
            'datetime' =>
            DateTime::__set_state([
                'date' => '2019-11-16 21:59:15.276562',
                'timezone_type' => 3,
                'timezone' => 'America/New_York',
            ]),
            'extra' =>
            [
                'custom_uri' => 'https://example.org',
                'custom_title' => 'Custom Title Placeholder',
            ],
        ];
        $this->defaultCard = [
            '@type' => 'MessageCard',
            '@context' => 'https://schema.org/extensions',
            'title' => 'title',
            'summary' => 'title',
            'sections' =>
            [
                [
                    'activityTitle' => 'An error has occurred',
                    'activitySubtitle' => '2019-11-16 21:59:15',
                    'facts' =>
                    [
                        [
                            'name' => 'level',
                            'value' => 'DEBUG',
                        ],
                        [
                            'name' => 'info',
                            'value' => 'items in the context array may be turned into "facts"',
                        ],
                        [
                            'name' => 'custom_uri',
                            'value' => 'https://example.org',
                        ],
                        [
                            'name' => 'custom_title',
                            'value' => 'Custom Title Placeholder',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function generateDefaultSection()
    {
        $reflected = new ReflectionClass('MessageCard\TeamsHandler');
        $method = $reflected->getMethod(__FUNCTION__);
        $method->setAccessible(true);
        return $method->invoke($this->handler, $this->record);
    }

    public function testDefaultMessageCardHasCorrectStructure()
    {
        $this->handler->getCard()->pushSection($this->generateDefaultSection());
        $this->assertEqualsCanonicalizing(
            json_decode($this->handler->getCard()->toJson(), true),
            $this->defaultCard
        );
    }

    public function testCustomCardDoesNotContainDefaultCardComponents()
    {
        $section = new Section();
        $section->title = 'Custom Section Title!';
        $this->handler->getCard()->pushSection($section);
        $this->assertEqualsCanonicalizing(
            json_decode($this->handler->getCard()->toJson(), true),
            [
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'title' => 'title',
                'summary' => 'title',
                'sections' => [
                    [
                        'title' => 'Custom Section Title!',
                    ],
                ],
            ]
        );
    }
}
