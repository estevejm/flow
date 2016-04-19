<?php

namespace EJM\Flow\Tests\Unit\Mapper\D3;

use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Mapper\D3\Layout;
use EJM\Flow\Mapper\D3\Link;
use EJM\Flow\Mapper\D3\Node;
use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node as NetworkNode;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;

class ForceLayoutMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidConfiguration()
    {
        new ForceLayoutMapper([]);
    }

    /**
     * @dataProvider mapDataProvider
     */
    public function testMap($config, $network, $layout)
    {
        $mapper = new ForceLayoutMapper($config);

        $this->assertEquals($layout, $mapper->map($network));
    }

    public function mapDataProvider()
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

        $network = new Network([$command1, $handler1, $event1, $subscriber1, $subscriber2]);

        return [
            'empty network' => [
                'config' => [
                    ForceLayoutMapper::MAP_HANDLERS => true,
                    ForceLayoutMapper::MAP_SUBSCRIBERS => true,
                ],
                'network' => new Network([]),
                'layout' => new Layout([],[]),
            ],
            'basic layout' => [
                'config' => [
                    ForceLayoutMapper::MAP_HANDLERS => true,
                    ForceLayoutMapper::MAP_SUBSCRIBERS => true,
                ],
                'network' => $network,
                'layout' => new Layout(
                    [
                        $node1 = new Node(0, 'command_1', NetworkNode::TYPE_COMMAND),
                        $node2 = new Node(1, 'handler_1', NetworkNode::TYPE_HANDLER),
                        $node3 = new Node(2, 'event_1', NetworkNode::TYPE_EVENT),
                        $node4 = new Node(3, 'subscriber_1', NetworkNode::TYPE_SUBSCRIBER),
                        $node5 = new Node(4, 'subscriber_2', NetworkNode::TYPE_SUBSCRIBER),
                    ],
                    [
                        new Link($node1, $node2),
                        new Link($node2, $node3),
                        new Link($node3, $node4),
                        new Link($node3, $node5),
                        new Link($node5, $node1),
                    ]
                ),
            ],
            'handler free layout' => [
                'config' => [
                    ForceLayoutMapper::MAP_HANDLERS => false,
                    ForceLayoutMapper::MAP_SUBSCRIBERS => true,
                ],
                'network' => $network,
                'layout' => new Layout(
                    [
                        $node1 = new Node(0, 'command_1', NetworkNode::TYPE_COMMAND),
                        $node3 = new Node(1, 'event_1', NetworkNode::TYPE_EVENT),
                        $node4 = new Node(2, 'subscriber_1', NetworkNode::TYPE_SUBSCRIBER),
                        $node5 = new Node(3, 'subscriber_2', NetworkNode::TYPE_SUBSCRIBER),
                    ],
                    [
                        new Link($node1, $node3),
                        new Link($node3, $node4),
                        new Link($node3, $node5),
                        new Link($node5, $node1),
                    ]
                ),
            ],
            'subscriber free layout' => [
                'config' => [
                    ForceLayoutMapper::MAP_HANDLERS => true,
                    ForceLayoutMapper::MAP_SUBSCRIBERS => false,
                ],
                'network' => $network,
                'layout' => new Layout(
                    [
                        $node1 = new Node(0, 'command_1', NetworkNode::TYPE_COMMAND),
                        $node2 = new Node(1, 'handler_1', NetworkNode::TYPE_HANDLER),
                        $node3 = new Node(2, 'event_1', NetworkNode::TYPE_EVENT),
                    ],
                    [
                        new Link($node1, $node2),
                        new Link($node2, $node3),
                        new Link($node3, $node1),
                    ]
                ),
            ],
            'only commands and events' => [
                'config' => [
                    ForceLayoutMapper::MAP_HANDLERS => false,
                    ForceLayoutMapper::MAP_SUBSCRIBERS => false,
                ],
                'network' => $network,
                'layout' => new Layout(
                    [
                        $node1 = new Node(0, 'command_1', NetworkNode::TYPE_COMMAND),
                        $node3 = new Node(1, 'event_1', NetworkNode::TYPE_EVENT),
                    ],
                    [
                        new Link($node1, $node3),
                        new Link($node3, $node1),
                    ]
                ),
            ],
        ];
    }
}
