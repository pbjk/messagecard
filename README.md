## About

This little library is mainly useful for sending messages to Microsoft Teams,
since Microsoft considers MessageCard a [legacy format][] (even though it is the
only format supported by Teams). For sending cards to Outlook, the [adaptive card format][]
is recommended.

## Basic Monolog logging

```php
use Monolog\Logger;
use MessageCard\Handler\TeamsHandler;

// Create a Monolog Logger as usual
$logger = new Logger('name');
$teams = new TeamsHandler('https://outlook.office.com/webhook/...');
$logger->pushHandler($teams);
$logger->critical('A fatal error has occurred');
```

## Placeholders

This package supports "moustache-style" placeholders that can be filled in just
before the message is sent.

Which doesn't add much when constructing and sending messages manually, but can
be helpful when the message is processed in some way before being sent. My use
case for placeholders was in the Monolog integration (the TeamsHandler class).
For example, in the two code snippets below, the message emitted by the Monolog
logger will contain a GitHub link to the file that caused the error.

### Using a custom callback function

```php
use Monolog\Processor\IntrospectionProcessor;
use MessageCard\Handler\TeamsHandler;
use MessageCard\Action\OpenUri;

$logger = new Logger('channel name');
$teams = new TeamsHandler('https://outlook.office.com/webhook/...');
$teams->pushProcessor(function ($record) {
    $record['extra']['repo_uri'] = 'https://github.com/user/repo/blob/master';
    $record['extra']['relative_file'] = str_replace('/var/www/html/', '', $record['extra']['file']);
    return $record;
});
// Use a Monolog IntrospectionProcessor to provide the path to the file that logged this message
// Push custom processors to the handler rather than the logger to avoid cluttering the output of other handlers
$teams->pushProcessor(new IntrospectionProcessor());
// The TeamsHandler will replace anything in double backets with any matching entries from the Monolog record (`$record['extra']`)
$teams->pushAction(new OpenUri('{{repo_uri}}/{{relative_file}}'));
$logger->pushHandler($teams);
$logger->critical('A fatal error has occurred');
```

### Using placeholder processors

```php
use MessageCard\Handler\TeamsHandler;
use MessageCard\Processor\RepoUriProcessor;

$logger = new Logger('channel name');
$uri_processor = new RepoUriProcessor('https://github.com/user/repo/blob/master', '/var/www/html');
$teams = new TeamsHandler('https://outlook.office.com/webhook/...');
$teams->pushProcessor($uri_processor);
$teams->pushAction(new OpenUri($uri_processor->getPlaceholder()));
$logger->pushHandler($teams);
$logger->critical('A fatal error has occurred');
```

Note: The MessageCard format itself supports the
[same type of placeholders][input value substitution], but they should all take
the form `<id>.value`, where `<id>` is the id of an Input element in the card.
For this reason I think it should be pretty easy to avoid having placeholders
replaced accidentally but please let me know if this is a problem :)

### Beyond Monolog

It is possible to build a card from scratch and send it manually:

```php
use MessageCard\MessageCard;

$card = new MessageCard('title', 'summary');
$card->send();
```

Almost all properties on these classes are public, and there are setter methods
for chaining. There is no validation for simple properties, like booleans,
urls, strings, but for some more complicated structures there is some
validation -- for example, the OpenUri Action needs to look something like the
json below, so we are validating the os type.

```json
"targets": [
    { "os": "default", "uri": "https://contoso.com/example" },
    { "os": "iOS", "uri": "contoso://example" },
    { "os": "android", "uri": "contoso://example" },
    { "os": "windows", "uri": "contoso://example" }
]
```

Below is an example of a little bit more complicated card:

```php
use MessageCard\MessageCard;
use MessageCard\Section;

$card = MessageCard::create('title', 'summary')
    ->setThemeColor('#8892bf')
    ->setText('This is the main body of the message card, but it can also contain Sections!')
    ->pushSection(
        Section::create()
          ->setStartGroup()
          ->setActivityTitle('This Section will be somewhat separated visually')
          ->pushImage('https://path/to/image', '`title` html attribute')
          ->pushAction(new OpenUri('https://path/to/article', 'View Article'))
);
$card->send();
```

## Monospace

MessageCard supports markdown by default, so if you send something like
`'Constant MESSAGECARD_DATETIME_FORMAT not defined'`, 'DATETIME' will be
italicized and the underscores will be invisible.

To help out with this, MessageCard components can be set to monospace using
`AbstractMessageCardEntity::formatMonospace()`.

## Thanks

Thanks to [Monolog\Handler\SlackHandler][slackhandler] for guidance!!

[input value substitution]: https://docs.microsoft.com/en-us/outlook/actionable-messages/message-card-reference#input-value-substitution
[legacy format]: https://docs.microsoft.com/en-us/outlook/actionable-messages/message-card-reference
[adaptive card format]: https://docs.microsoft.com/en-us/outlook/actionable-messages/adaptive-card
[slackhandler]: https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SlackHandler.php
