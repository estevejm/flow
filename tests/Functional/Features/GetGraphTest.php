<?php

namespace EJM\Flow\Tests\Functional\Features;

use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Splitter;
use EJM\Flow\Tests\Functional\TestHelper;
use PHPUnit_Framework_TestCase;

class GetGraphTest extends PHPUnit_Framework_TestCase
{
    public function testAction()
    {
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

        $network = TestHelper::getNetwork();
        $splitter = new Splitter();
        $mapper = new ForceLayoutMapper();

        $networks = $splitter->split($network);

        $graph = array_map(
            function($network) use ($mapper) {
                return $mapper->map($network);
            },
            $networks
        );

        $this->assertEquals($expectedGraph, TestHelper::objectToArray($graph));
    }
}
