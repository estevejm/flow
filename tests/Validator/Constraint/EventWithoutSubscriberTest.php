<?php

namespace EJM\Flow\Tests\Validator\Constraint;

use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;
use EJM\Flow\Validator\Constraint\EventWithoutSubscriber;
use PHPUnit_Framework_TestCase;

class EventWithoutSubscriberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider supportsNodeDataProvider
     */
    public function testSupportsNode($node, $expected)
    {
        $constraint = new EventWithoutSubscriber();
        $this->assertEquals($expected, $constraint->supportsNode($node));
    }

    public function supportsNodeDataProvider()
    {
        return [
            'command' => [
                'node' => new Command('command_1'),
                'expected' => false,
            ],
            'handler' => [
                'node' => new Handler('handler_1', '\EJM\Flow\Network\Node\Handler', new Command('command_1')),
                'expected' => false,
            ],
            'event' => [
                'node' => new Event('event_1'),
                'expected' => true,
            ],
            'subscriber' => [
                'node' => new Subscriber('subscriber_1', '\EJM\Flow\Network\Node\Subscriber', new Event('event_1')),
                'expected' => false,
            ],
        ];
    }

    public function testValidateWithInvalidEvent()
    {
        $event = new Event('event_1');

        $constraint = new EventWithoutSubscriber();
        $violations = $constraint->validate($event);

        $this->assertCount(1, $violations);
        $this->assertEquals($event, $violations[0]->getNode());
    }

    public function testValidateValidEvent()
    {
        $event = new Event('event_1');
        $event->addSubscriber(new Subscriber('subscriber_1', '\EJM\Flow\Network\Node\Subscriber', $event));

        $constraint = new EventWithoutSubscriber();
        $violations = $constraint->validate($event);

        $this->assertEmpty($violations);
    }
}
 