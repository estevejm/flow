<?php

namespace EJM\Flow\Tests\Functional\Features;

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
}
