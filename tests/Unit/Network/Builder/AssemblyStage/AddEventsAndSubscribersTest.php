<?php

namespace EJM\Flow\Tests\Unit\Network\Builder\AssemblyStage;

use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Subscriber;
use PHPUnit_Framework_TestCase;

class AddEventsAndSubscribersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider assemblyStageMapProvider
     */
    public function testAssemble($map, $expectedEvents, $expectedSubscribers)
    {
        $blueprint = new Blueprint();

        $stage = new AddEventsAndSubscribers($map);

        $stage->assemble($blueprint);

        $this->assertEquals($expectedEvents, $blueprint->getEvents());
        $this->assertEquals($expectedSubscribers, $blueprint->getPublishers());
    }

    public function assemblyStageMapProvider()
    {
        $event1 = new Event('event_1');
        $subscriber1 = new Subscriber('subscriber_1');
        $subscriber1->subscribesTo($event1);

        $event2 = new Event('event_2');
        $event3 = new Event('event_3');
        $subscriber2 = new Subscriber('subscriber_2');
        $subscriber3 = new Subscriber('subscriber_3');
        $subscriber2->subscribesTo($event2);
        $subscriber3
            ->subscribesTo($event2)
            ->subscribesTo($event3);


        return [
            'empty map' => [
                'map' => [],
                'events' => [],
                'subscribers' => [],
            ],
            'basic map' => [
                'map' => [
                    'event_1' => [
                        [
                            'id' => 'subscriber_1',
                            'class' => null,
                        ]
                    ]
                ],
                'events' => [
                    'event_1' => $event1,
                ],
                'subscribers' => [
                    'subscriber_1' => $subscriber1,
                ],
            ],
            'complex map' => [
                'map' => [
                    'event_1' => [
                        [
                            'id' => 'subscriber_1',
                            'class' => null,
                        ],
                    ],
                    'event_2' => [
                        [
                            'id' => 'subscriber_2',
                            'class' => null,
                        ],
                        [
                            'id' => 'subscriber_3',
                            'class' => null,
                        ],
                    ],
                    'event_3' => [
                        [
                            'id' => 'subscriber_3',
                            'class' => null,
                        ],
                    ],
                ],
                'events' => [
                    'event_1' => $event1,
                    'event_2' => $event2,
                    'event_3' => $event3,
                ],
                'subscribers' => [
                    'subscriber_1' => $subscriber1,
                    'subscriber_2' => $subscriber2,
                    'subscriber_3' => $subscriber3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider assemblyStageInvalidMapProvider
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testAssembleWithInvalidMap($map)
    {
        new AddEventsAndSubscribers($map);
    }

    public function assemblyStageInvalidMapProvider()
    {
        return [
            'items not array' => [
                'map' => ['event1' => 'subscriber1']
            ],
            'items not array of arrays' => [
                'map' => ['event1' => ['subscriber1']]
            ],
            'array of arrays item is empty' => [
                'map' => [
                    'event1' => [
                        []
                    ]
                ]
            ],
            'items array with missing id' => [
                'map' => [
                    'event1' => [
                        [
                            'class' => '1',
                        ],
                    ],
                ]
            ],
            'items array with missing class' => [
                'map' => [
                    'event1' => [
                        [
                            'id' => '1',
                        ],
                    ],
                ]
            ],
            'key is not string' => [
                'map' => [
                    1 => [
                        [
                            'id' => 'id_1',
                            'class' => 'class_1',
                        ]
                    ],
                ]
            ]
        ];
    }
}
