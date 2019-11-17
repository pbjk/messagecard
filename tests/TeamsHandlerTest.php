<?php

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Dialogue\TeamsHandler;
use Dialogue\MessageCard\Section;

final class TeamsHandlerTest extends TestCase
{

    protected $handler;
    protected $record;
    protected $defaultCard;

    protected function setUp(): void
    {
        $this->handler = new TeamsHandler('uri', Logger::DEBUG, 'title');
        $this->record = array(
            'message' => 'An error has occurred',
            'context' =>
            array(
                'info' => 'items in the context array may be turned into "facts"',
            ),
            'level' => 100,
            'level_name' => 'DEBUG',
            'channel' => 'channel_name',
            'datetime' =>
            DateTime::__set_state(array(
                'date' => '2019-11-16 21:59:15.276562',
                'timezone_type' => 3,
                'timezone' => 'America/New_York',
            )),
            'extra' =>
            array(
                'custom_uri' => 'https://example.org',
                'custom_title' => 'Custom Title Placeholder',
            ),
        );
        $this->defaultCard = array(
            '@type' => 'MessageCard',
            '@context' => 'https://schema.org/extensions',
            'title' => 'title',
            'sections' =>
            array(
                array(
                    'activityTitle' => 'An error has occurred',
                    'activitySubtitle' => '2019-11-16 21:59:15',
                    'facts' =>
                    array(
                        array(
                            'name' => 'level',
                            'value' => 'DEBUG',
                        ),
                        array(
                            'name' => 'info',
                            'value' => 'items in the context array may be turned into "facts"',
                        ),
                        array(
                            'name' => 'custom_uri',
                            'value' => 'https://example.org',
                        ),
                        array(
                            'name' => 'custom_title',
                            'value' => 'Custom Title Placeholder',
                        ),
                    ),
                ),
            ),
        );
    }

    // Even the `write` function is private, so we are definitely going to have to use reflection
    private function generateMessageCard()
    {
        $reflected = new ReflectionClass('Dialogue\TeamsHandler');
        $method = $reflected->getMethod(__FUNCTION__);
        $method->setAccessible(true);
        return $method->invoke($this->handler, $this->record);
    }

    public function testDefaultMessageCardHasCorrectStructure()
    {
        $this->assertEqualsCanonicalizing(
            json_decode($this->generateMessageCard(), true),
            $this->defaultCard
        );
    }

    public function testPlaceholdersInCardTitleAreReplaced()
    {
        $this->handler->getCard()->title = '{{custom_title}}';
        $this->assertEqualsCanonicalizing(
            json_decode($this->generateMessageCard(), true),
            array_merge($this->defaultCard, array(
                'title' => 'Custom Title Placeholder',
            ))
        );
    }

    public function testCustomCardDoesNotContainDefaultCardComponents()
    {
        $section = new Section();
        $section->title = 'Custom Section Title!';
        $this->handler->getCard()->pushSection($section);
        $this->assertEqualsCanonicalizing(
            json_decode($this->generateMessageCard(), true),
            array(
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'title' => 'title',
                'sections' => array(
                    array(
                        'title' => 'Custom Section Title!',
                    ),
                ),
            )
        );
    }

    public function testMonospaceGeneratesBacktickEscapes()
    {
        $this->handler->monospace();
        $this->assertEqualsCanonicalizing(
            json_decode($this->generateMessageCard(), true),
            array(
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'title' => 'title',
                'sections' =>
                array(
                    array(
                        'activityTitle' => '`An error has occurred`',
                        'activitySubtitle' => '`2019-11-16 21:59:15`',
                        'facts' =>
                        array(
                            array(
                                'name' => '`level`',
                                'value' => '`DEBUG`',
                            ),
                            array(
                                'name' => '`info`',
                                'value' => '`items in the context array may be turned into "facts"`',
                            ),
                            array(
                                'name' => '`custom_uri`',
                                'value' => '`https://example.org`',
                            ),
                            array(
                                'name' => '`custom_title`',
                                'value' => '`Custom Title Placeholder`',
                            ),
                        ),
                    ),
                ),
            )
        );
    }
}
