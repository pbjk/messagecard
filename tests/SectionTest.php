<?php

namespace Tests;

use MessageCard\Action\OpenUri;
use MessageCard\Section;
use PHPUnit\Framework\TestCase;

class SectionTest extends TestCase
{
    public function testSetAllPropertiesOfSection()
    {
        $section = Section::create()
            ->setTitle('section title')
            ->setText('section text')
            ->startGroup()
            ->setActivityTitle('activity title')
            ->setActivitySubtitle('activity subtitle')
            ->setActivityText('activity text')
            ->setFacts(['fact1' => 'interesting', 'fact2' => 'fascinating'])
            ->setActivityImage('https://example.com/image1', 'image title')
            ->setHeroImage('https://example.com/image1', 'image title')
            ->pushImage('https://example.com/image1', 'image title')
            ->pushAction(new OpenUri('https://example.com/openuri'));

        $this->assertEqualsCanonicalizing(
            json_decode(json_encode($section), true),
            [
                'title' => 'section title',
                'text' => 'section text',
                'startGroup' => true,
                'activityTitle' => 'activity title',
                'activitySubtitle' => 'activity subtitle',
                'activityText' => 'activity text',
                'facts' => [
                    ['name' => 'fact1', 'value' => 'interesting'],
                    ['name' => 'fact2', 'fact2' => 'fascinating'],
                ],
                'activityImage' => 'https://example.com/image1',
                'heroImage' => ['image' => 'https://example.com/image1', 'title' => 'image title'],
                'images' => [
                    ['image' => 'https://example.com/image1', 'title' => 'image title'],
                ],
                'potentialAction' => [
                    [
                        'targets' =>
                        [
                            [
                                'os' => 'default',
                                'uri' => 'https://example.com/openuri',
                            ],
                        ],
                        '@type' => 'OpenUri',
                        'name' => 'Open Link',
                    ],
                ],
            ],
        );
    }
}
