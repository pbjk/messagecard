<?php

use PHPUnit\Framework\TestCase;
use Dialogue\MessageCard\MessageCard;

final class MessageCardTest extends Testcase
{
    public function testSetBasicPropertiesOfMessageCard()
    {
        $card = MessageCard::new('card title')
            ->setSummary('card summary')
            ->setCorrelationId('3081-1289483-1291832')
            ->setThemeColor('#300330')
            ->setText('card text');

        $this->assertEqualsCanonicalizing(
            json_decode(json_encode($card), true),
            array(
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'title' => 'card title',
                'summary' => 'card summary',
                'correlationId' => '3081-1289483-1291832',
                'themeColor' => '#300330',
                'text' => 'card text',
            )
        );
    }
}
