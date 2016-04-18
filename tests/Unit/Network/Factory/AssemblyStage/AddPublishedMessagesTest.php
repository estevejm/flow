<?php

namespace EJM\Flow\Tests\Unit\Network\Factory\AssemblyStage;

use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Factory\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;
use PHPUnit_Framework_TestCase;

class AddPublishedMessagesTest extends PHPUnit_Framework_TestCase
{
    public function testAssemble()
    {
        $handler1 = new Handler('handler_1', '\EJM\Flow\Network\Node\Handler');
        $command1 = new Command('command_1', $handler1);

        $event1 = new Event('event_1');
        $subscriber1 = new Subscriber('subscriber_1', '\EJM\Flow\Network\Node\Subscriber', $event1);

        $blueprint = new Blueprint();
        $blueprint
            ->addCommand($command1)
            ->addMessagePublisher($handler1)
            ->addEvent($event1)
            ->addMessagePublisher($subscriber1);

        $collector = $this->getMockBuilder('\EJM\Flow\Collector\Collector')
            ->disableOriginalConstructor()
            ->getMock();

        $collector->expects($this->exactly(2))
            ->method('collect')
            ->will($this->returnValueMap([
                ['\EJM\Flow\Network\Node\Handler', ['event_1']],
                ['\EJM\Flow\Network\Node\Subscriber', ['command_1', 'event_2']],
            ]));

        $stage = new AddPublishedMessages($collector);

        $stage->assemble($blueprint);

        $this->assertContains($event1, $blueprint->getMessagePublisher('handler_1')->getMessages());
        $this->assertContains($command1, $blueprint->getMessagePublisher('subscriber_1')->getMessages());
        $this->assertTrue($blueprint->hasEvent('event_2'));
        $this->assertContains(
            $blueprint->getEvent('event_2'),
            $blueprint->getMessagePublisher('subscriber_1')->getMessages()
        );
    }
}
