<?php

namespace EJM\Flow\Tests\Integration\Features;

use EJM\Flow\Collector\MessagesToPublishCollector;
use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Splitter;
use PHPUnit_Framework_TestCase;

class GetGraphTest extends PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $mapperConfig = [
            ForceLayoutMapper::MAP_HANDLERS => true,
            ForceLayoutMapper::MAP_SUBSCRIBERS => true,
        ];

        $commandHandlerMap = [
            'execute_command' => [
                'id' => 'execute_command_handler',
                'class' => 'EJM\\Flow\\Tests\\Functional\\Sandbox\\SimpleBus\\Command\\ExecuteCommandHandler',
            ],
            'execute_command_2' => [
                'id' => 'execute_command_2_handler',
                'class' => 'EJM\\Flow\\Tests\\Functional\\Sandbox\\SimpleBus\\Command\\ExecuteCommand2Handler',
            ],
        ];

        $eventSubscribersMap = [
            'command_executed' => [
                [
                    'id' => 'log_command_executed',
                    'class' => 'EJM\\Flow\\Tests\\Functional\\Sandbox\\SimpleBus\\Subscriber\\LogCommandExecuted',
                ],
                [
                    'id' => 'trigger_execute_command_2',
                    'class' => 'EJM\\Flow\\Tests\\Functional\\Sandbox\\SimpleBus\\Subscriber\\TriggerExecuteCommand2',
                ],
            ],
        ];

        $expectedGraph = [
            [
                'nodes' =>[
                    [
                        'id' => 'execute_command',
                        'type' => 'command',
                    ],
                    [
                        'id' => 'execute_command_handler',
                        'type' => 'handler',
                    ],
                    [
                        'id' => 'command_executed',
                        'type' => 'event',
                    ],
                    [
                        'id' => 'log_command_executed',
                        'type' => 'subscriber',
                    ],
                    [
                        'id' => 'trigger_execute_command_2',
                        'type' => 'subscriber',
                    ],
                    [
                        'id' => 'execute_command_2',
                        'type' => 'command',
                    ],
                    [
                        'id' => 'execute_command_2_handler',
                        'type' => 'handler',
                    ],
                    [
                        'id' => 'command_2_executed',
                        'type' => 'event',
                    ],
                ],
                'links' =>[
                    [
                        'source' => 0,
                        'target' => 1,
                    ],
                    [
                        'source' => 1,
                        'target' => 2,
                    ],
                    [
                        'source' => 2,
                        'target' => 3,
                    ],
                    [
                        'source' => 2,
                        'target' => 4,
                    ],
                    [
                        'source' => 4,
                        'target' => 5,
                    ],
                    [
                        'source' => 5,
                        'target' => 6,
                    ],
                    [
                        'source' => 6,
                        'target' => 7,
                    ],
                    [
                        'source' => 6,
                        'target' => 2,
                    ],
                    [
                        'source' => 6,
                        'target' => 0,
                    ],
                    [
                        'source' => 4,
                        'target' => 7,
                    ],
                ],
            ],
        ];

        $builder = new Builder();
        $builder->withAssemblyStage(new AddCommandsAndHandlers($commandHandlerMap));
        $builder->withAssemblyStage(new AddEventsAndSubscribers($eventSubscribersMap));
        $builder->withAssemblyStage(new AddPublishedMessages(new MessagesToPublishCollector()));

        $network = $builder->build();

        $splitter = new Splitter();

        $networks = $splitter->split($network);

        $mapper = new ForceLayoutMapper($mapperConfig);

        $graph = array_map(
            function($network) use ($mapper) {
                return $mapper->map($network);
            },
            $networks
        );

        $this->assertEquals($expectedGraph, json_decode(json_encode($graph), true));
    }
}
