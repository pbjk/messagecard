<?php

namespace Tests;

use InvalidArgumentException;
use MessageCard\Action\OpenUri;
use PHPUnit\Framework\TestCase;

class OpenUriTest extends TestCase
{
    public function testCanCreateTargetsFromArray()
    {
        $this->assertInstanceOf(
            OpenUri::class,
            new OpenUri(array(
                array('os' => 'default', 'uri' => 'https://contoso.com'),
                array('os' => 'android', 'uri' => 'contoso://contoso.com'),
            ))
        );
    }

    public function testCanCreateTargetsFromString()
    {
        $this->assertInstanceOf(
            OpenUri::class,
            new OpenUri('https://contoso.com')
        );
    }

    public function testCannotCreateTargetsFromInvalidOsType()
    {
        $this->expectException(InvalidArgumentException::class);
        new OpenUri(array(
            array('os' => 'deefault', 'uri' => 'https://contoso.com'),
        ));
    }

    public function testCannotCreateTargetsFromArrayUris()
    {
        $this->expectException(InvalidArgumentException::class);
        new OpenUri(array(
            array('os' => 'default', 'uri' => array('https://contoso.com', 'https://example.com')),
        ));
    }

    public function testCannotCreateTargetsWhenMissingRequiredArrayKeys()
    {
        $this->expectException(InvalidArgumentException::class);
        new OpenUri(array(
            array('uri' => 'https://contoso.com'),
        ));
    }

    public function testConstructionResultsInProperFormat()
    {
        $this->assertEqualsCanonicalizing(
            json_decode(json_encode((new OpenUri(array(
                array('os' => 'default', 'uri' => 'https://contoso.com'),
                array('os' => 'android', 'uri' => 'contoso://contoso.com'),
            )))), true),
            array(
                '@type' => 'OpenUri',
                'name' => 'Open Link',
                'targets' => array(
                    array('os' => 'default', 'uri' => 'https://contoso.com'),
                    array('os' => 'android', 'uri' => 'contoso://contoso.com'),
                )
            )
        );
    }
}
