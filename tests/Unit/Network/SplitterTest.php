<?php

namespace EJM\Flow\Tests\Unit\Network;

use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;
use EJM\Flow\Network\Splitter;
use PHPUnit_Framework_TestCase;

class SplitterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testSplit($network, $expected)
    {
        $splitter = new Splitter();

        $networks = $splitter->split($network);

        $this->assertEquals($expected, $networks);
    }

    public function mapDataProvider()
    {
        $connectedNetwork = $this->getConnectedNetwork();

        $handler2 = new Handler('handler_2');
        $command2 = new Command('command_2', $handler2);

        $event2 = new Event('event_2');
        $subscriber3 = new Subscriber('subscriber_3');
        $subscriber3->subscribesTo($event2);

        return [
            'empty network' => [
                'network' => new Network([]),
                'expected' => [],
            ],
            'connected network' => [
                'network' => $connectedNetwork,
                'expected' => [$connectedNetwork],
            ],
            'splitted network' => [
                'network' => new Network([
                    'command_2' => $command2,
                    'handler_2' => $handler2,
                    'event_2' => $event2,
                    'subscriber_3' => $subscriber3,
                ]),
                'expected' => [
                    new Network([
                        'command_2' => $command2,
                        'handler_2' => $handler2,
                    ]),
                    new Network([
                        'event_2' => $event2,
                        'subscriber_3' => $subscriber3,
                    ]),
                ],
            ],
        ];
    }

    /**
     * @return Network
     */
    private function getConnectedNetwork()
    {
        $handler1 = new Handler('handler_1');
        $command1 = new Command('command_1', $handler1);
        $event1 = new Event('event_1');
        $subscriber1 = new Subscriber('subscriber_1');
        $subscriber2 = new Subscriber('subscriber_2');
        $subscriber1->subscribesTo($event1);
        $subscriber2->subscribesTo($event1);

        $handler1->addMessage($event1);
        $subscriber2->addMessage($command1);

        return new Network([
            'command_1' => $command1,
            'handler_1' => $handler1,
            'event_1' => $event1,
            'subscriber_1' => $subscriber1,
            'subscriber_2' => $subscriber2,
        ]);
    }
}
