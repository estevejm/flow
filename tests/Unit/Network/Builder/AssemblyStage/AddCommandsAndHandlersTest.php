<?php

namespace EJM\Flow\Tests\Unit\Network\Builder\AssemblyStage;

use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Handler;
use PHPUnit_Framework_TestCase;

class AddCommandsAndHandlersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider assemblyStageMapProvider
     */
    public function testAssemble($map, $expectedCommands, $expectedHandlers)
    {
        $blueprint = new Blueprint();

        $stage = new AddCommandsAndHandlers($map);

        $stage->assemble($blueprint);

        $this->assertEquals($expectedCommands, $blueprint->getCommands());
        $this->assertEquals($expectedHandlers, $blueprint->getPublishers());
    }

    public function assemblyStageMapProvider()
    {
        $handler1 = new Handler('handler_1');
        $command1 = new Command('command_1', $handler1);

        $handler2 = new Handler('handler_2');
        $command2 = new Command('command_2', $handler2);
        $command3 = new Command('command_3', $handler2);

        return [
            'empty map' => [
                'map' => [],
                'commands' => [],
                'handlers' => [],
            ],
            'basic map' => [
                'map' => [
                    'command_1' => [
                        'id' => 'handler_1',
                        'class' => null,
                    ]
                ],
                'commands' => [
                    'command_1' => $command1,
                ],
                'handlers' => [
                    'handler_1' => $handler1,
                ],
            ],
            'complex map' => [
                'map' => [
                    'command_1' => [
                        'id' => 'handler_1',
                        'class' => null,
                    ],
                    'command_2' => [
                        'id' => 'handler_2',
                        'class' => null,
                    ],
                    'command_3' => [
                        'id' => 'handler_2',
                        'class' => null,
                    ],
                ],
                'commands' => [
                    'command_1' => $command1,
                    'command_2' => $command2,
                    'command_3' => $command3,
                ],
                'handlers' => [
                    'handler_1' => $handler1,
                    'handler_2' => $handler2,
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
        new AddCommandsAndHandlers($map);
    }

    public function assemblyStageInvalidMapProvider()
    {
        return [
            'items not array' => [
                'map' => ['command1' => 'handler1']
            ],
            'items is empty array' => [
                'map' => ['command1' => []]
            ],
            'items array with missing id' => [
                'map' => [
                    'command1' => [
                        'class' => '1',
                    ],
                ]
            ],
            'items array with missing class' => [
                'map' => [
                    'command1' => [
                        'id' => '1',
                    ],
                ]
            ],
            'key is not string' => [
                'map' => [
                    1 => [
                        'id' => 'id_1',
                        'class' => 'class_1',
                    ],
                ]
            ]
        ];
    }
}
