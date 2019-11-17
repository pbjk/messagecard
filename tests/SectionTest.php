<?php

use PHPUnit\Framework\TestCase;
use Dialogue\MessageCard\Section;
use Dialogue\MessageCard\Action\OpenUri;

final class SectionTest extends TestCase
{
    public function testSetAllPropertiesOfSection()
    {
        $section = Section::new()
            ->setTitle('section title')
            ->setText('section text')
            ->setStartGroup(true)
            ->setActivityTitle('activity title')
            ->setActivitySubtitle('activity subtitle')
            ->setActivityText('activity text')
            ->setFacts(array('fact1' => 'interesting', 'fact2' => 'fascinating'))
            ->setActivityImage('https://example.com/image1', 'image title')
            ->setHeroImage('https://example.com/image1', 'image title')
            ->pushImage('https://example.com/image1', 'image title')
            ->pushPotentialAction(new OpenUri('https://example.com/openuri'));

        $this->assertEqualsCanonicalizing(
            json_decode(json_encode($section), true),
            array(
                'title' => 'section title',
                'text' => 'section text',
                'startGroup' => true,
                'activityTitle' => 'activity title',
                'activitySubtitle' => 'activity subtitle',
                'activityText' => 'activity text',
                'facts' => array(
                    array('name' => 'fact1', 'value' => 'interesting'),
                    array('name' => 'fact2', 'fact2' => 'fascinating'),
                ),
                'activityImage' => array('image' => 'https://example.com/image1', 'title' => 'image title'),
                'heroImage' => array('image' => 'https://example.com/image1', 'title' => 'image title'),
                'images' => array(
                    array('image' => 'https://example.com/image1', 'title' => 'image title'),
                ),
                'potentialAction' => array(
                    array(
                        'targets' =>
                        array(
                            array(
                                'os' => 'default',
                                'uri' => 'https://example.com/openuri',
                            ),
                        ),
                        '@type' => 'OpenUri',
                        'name' => 'Open Link',
                    ),
                ),
            ),
        );
    }
}
