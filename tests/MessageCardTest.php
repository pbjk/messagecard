<?php

namespace Tests;

use MessageCard\MessageCard;
use MessageCard\Section;
use PHPUnit\Framework\TestCase;

class MessageCardTest extends Testcase
{
    protected $card;
    protected $defaultCard;

    protected function setUp(): void
    {
        $this->card = MessageCard::create('card title')
            ->setSummary('card summary')
            ->setCorrelationId('3081-1289483-1291832')
            ->setThemeColor('#300330')
            ->setText('card text');
        $this->defaultCard = array(
            '@type' => 'MessageCard',
            '@context' => 'https://schema.org/extensions',
            'title' => 'card title',
            'summary' => 'card summary',
            'correlationId' => '3081-1289483-1291832',
            'themeColor' => '#300330',
            'text' => 'card text',
        );
    }

    public function testSetBasicPropertiesOfMessageCard()
    {

        $this->assertEqualsCanonicalizing(
            json_decode($this->card->toJson(), true),
            $this->defaultCard
        );
    }

    public function testPlaceholdersInCardTitleAreReplaced()
    {
        $this->card->setTitle('{{custom_title}}');
        $this->assertEqualsCanonicalizing(
            json_decode($this->card->toJson(array('custom_title' => 'Custom Title Placeholder')), true),
            array_merge($this->defaultCard, array(
                'title' => 'Custom Title Placeholder',
            ))
        );
    }

    public function testMonospaceGeneratesBacktickEscapes()
    {
        $this->card->pushSection(Section::create()->setTitle('Monospace title')->formatMonospace());
        $this->assertEqualsCanonicalizing(
            json_decode($this->card->toJson(), true),
            array(
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'title' => 'card title',
                'summary' => 'card summary',
                'correlationId' => '3081-1289483-1291832',
                'themeColor' => '#300330',
                'text' => 'card text',
                'sections' => array(
                    array(
                        'title' => '`Monospace title`',
                    ),
                ),
            )
        );
    }
}
