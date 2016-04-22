<?php

namespace EJM\Flow\Tests\Functional\Features;

use EJM\Flow\Collector\Collector;
use EJM\Flow\Collector\Parser\Visitor\MessagesToPublishNodeVisitor;
use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Splitter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetGraphTest extends WebTestCase
{

    public function testAction()
    {
        $expectedResponse = [
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
        
        $client = static::createClient();
        $client->request('GET', '/flow/graphs');
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testPackageAction()
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

        $expectedResponse = [
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

        $visitor = new MessagesToPublishNodeVisitor();
        $collector = new Collector($visitor);

        $builder = new Builder();
        $builder->withAssemblyStage(new AddCommandsAndHandlers($commandHandlerMap));
        $builder->withAssemblyStage(new AddEventsAndSubscribers($eventSubscribersMap));
        $builder->withAssemblyStage(new AddPublishedMessages($collector));

        $network = $builder->build();

        $splitter = new Splitter();

        $networks = $splitter->split($network);

        $mapper = new ForceLayoutMapper($mapperConfig);

        $result = array_map(
            function($network) use ($mapper) {
                return $mapper->map($network);
            },
            $networks
        );

        $this->assertEquals($expectedResponse, json_decode(json_encode($result), true));
    }
}
