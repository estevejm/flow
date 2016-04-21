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

        $handler1 = new Handler('handler_1');
        $command1 = new Command('command_1', $handler1);
        $event1 = new Event('event_1');
        $handler1->addMessage($event1);
        $subscriber1 = new Subscriber('subscriber_1');
        $subscriber1->subscribesTo($event1);
        $handler2 = new Handler('handler_2');
        $command2 = new Command('command_2', $handler2);
        $subscriber1->addMessage($command2);

        $handler3 = new Handler('handler_3');
        $command3 = new Command('command_3', $handler3);
        $command4 = new Command('command_4', $handler3);
        $event2 = new Event('event_2');
        $event3 = new Event('event_3');
        $handler3->addMessage($event2)->addMessage($event3);
        $handler4 = new Handler('handler_4');
        $handler4->addMessage($event3);
        $subscriber2 = new Subscriber('subscriber_2');
        $subscriber3 = new Subscriber('subscriber_3');
        $subscriber2->subscribesTo($event3);
        $subscriber3->subscribesTo($event3);
        $event4 = new Event('event_4');
        $event5 = new Event('event_5');
        $subscriber2->addMessage($event4)->addMessage($event5);

        $subscriber4 = new Subscriber('subscriber_4');
        $subscriber5 = new Subscriber('subscriber_5');
        $handler5 = new Handler('handler_5');
        $command5 = new Command('command_5', $handler5);
        $subscriber4->addMessage($command5);
        $event6 = new Event('event_6');
        $subscriber4->addMessage($event6);
        $subscriber5->addMessage($event6);
        $event7 = new Event('event_7');
        $subscriber6 = new Subscriber('subscriber_6');
        $subscriber6->subscribesTo($event6)->subscribesTo($event7);
        $handler6 = new Handler('handler_6');
        $command6 = new Command('command_6', $handler6);
        $handler7 = new Handler('handler_7');
        $command7 = new Command('command_7', $handler7);
        $subscriber6->addMessage($command6)->addMessage($command7);

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
                    'command_1' => $command1,
                    'handler_1' => $handler1,
                    'event_1' => $event1,
                    'subscriber_1' => $subscriber1,
                    'command_2' => $command2,
                    'handler_2' => $handler2,
                    'command_3' => $command3,
                    'command_4' => $command4,
                    'handler_3' => $handler3,
                    'handler_4' => $handler4,
                    'event_2' => $event2,
                    'event_3' => $event3,
                    'subscriber_2' => $subscriber2,
                    'subscriber_3' => $subscriber3,
                    'event_4' => $event4,
                    'event_5' => $event5,
                    'subscriber_4' => $subscriber4,
                    'subscriber_5' => $subscriber5,
                    'command_5' => $command5,
                    'handler_5' => $handler5,
                    'event_6' => $event6,
                    'event_7' => $event7,
                    'subscriber_6' => $subscriber6,
                    'command_6' => $command6,
                    'handler_6' => $handler6,
                    'command_7' => $command7,
                    'handler_7' => $handler7,
                ]),
                'expected' => [
                    new Network([
                        'command_1' => $command1,
                        'handler_1' => $handler1,
                        'event_1' => $event1,
                        'subscriber_1' => $subscriber1,
                        'command_2' => $command2,
                        'handler_2' => $handler2,
                    ]),
                    new Network([
                        'command_3' => $command3,
                        'command_4' => $command4,
                        'handler_3' => $handler3,
                        'handler_4' => $handler4,
                        'event_2' => $event2,
                        'event_3' => $event3,
                        'subscriber_2' => $subscriber2,
                        'subscriber_3' => $subscriber3,
                        'event_4' => $event4,
                        'event_5' => $event5,
                    ]),
                    new Network([
                        'subscriber_4' => $subscriber4,
                        'subscriber_5' => $subscriber5,
                        'command_5' => $command5,
                        'handler_5' => $handler5,
                        'event_6' => $event6,
                        'event_7' => $event7,
                        'subscriber_6' => $subscriber6,
                        'command_6' => $command6,
                        'handler_6' => $handler6,
                        'command_7' => $command7,
                        'handler_7' => $handler7,
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
