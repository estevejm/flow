<?php

namespace EJM\Flow\Tests\Validator\Constraint;

use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;
use EJM\Flow\Validator\Constraint\HandlerTriggersCommand;
use PHPUnit_Framework_TestCase;

class HandlerTriggersCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider supportsNodeDataProvider
     */
    public function testSupportsNode($node, $expected)
    {
        $constraint = new HandlerTriggersCommand();
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
                'expected' => true,
            ],
            'event' => [
                'node' => new Event('event_1'),
                'expected' => false,
            ],
            'subscriber' => [
                'node' => new Subscriber('subscriber_1', '\EJM\Flow\Network\Node\Subscriber', new Event('event_1')),
                'expected' => false,
            ],
        ];
    }

    public function testValidateWithInvalidEvent()
    {
        $handler = new Handler('handler_1', '\EJM\Flow\Network\Node\Handler', new Command('command_1'));
        $handler->addMessage(new Command('command_2'))->addMessage(new Event('event_1'));

        $constraint = new HandlerTriggersCommand();
        $violations = $constraint->validate($handler);

        $this->assertCount(1, $violations);
        $this->assertEquals($handler, $violations[0]->getNode());
    }

    public function testValidateWithValidEvent()
    {
        $handler = new Handler('handler_1', '\EJM\Flow\Network\Node\Handler', new Command('command_1'));
        $handler->addMessage(new Event('event_1'));

        $constraint = new HandlerTriggersCommand();
        $violations = $constraint->validate($handler);

        $this->assertEmpty($violations);
    }
}
