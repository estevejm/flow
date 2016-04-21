<?php

namespace EJM\Flow\Tests\Unit\Validator\Constraint;

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
        $handler = new Handler('handler_1');
        $command = new Command('command_1', $handler);

        return [
            'command' => [
                'node' => $command,
                'expected' => false,
            ],
            'handler' => [
                'node' => $handler,
                'expected' => true,
            ],
            'event' => [
                'node' => new Event('event_1'),
                'expected' => false,
            ],
            'subscriber' => [
                'node' => new Subscriber('subscriber_1'),
                'expected' => false,
            ],
        ];
    }

    public function testValidateWithInvalidEvent()
    {
        $handler1 = new Handler('handler_1');
        $command1 = new Command('command_1', $handler1);

        $handler2 = new Handler('handler_2');
        $command2 = new Command('command_2', $handler2);

        $handler1
            ->publishes($command2)
            ->publishes(new Event('event_1'));

        $constraint = new HandlerTriggersCommand();
        $violations = $constraint->validate($handler1);

        $this->assertCount(1, $violations);
        $this->assertEquals($handler1, $violations[0]->getNode());
    }

    public function testValidateWithValidEvent()
    {
        $handler = new Handler('handler_1');
        $command = new Command('command_1', $handler);
        $handler->publishes(new Event('event_1'));

        $constraint = new HandlerTriggersCommand();
        $violations = $constraint->validate($handler);

        $this->assertEmpty($violations);
    }
}
