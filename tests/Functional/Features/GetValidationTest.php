<?php

namespace EJM\Flow\Tests\Functional\Features;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetValidationTest extends WebTestCase
{

    public function testAction()
    {
        $expectedResponse = [
            'status' => 'invalid',
            'violations' => [
                [
                    'nodeId' => 'command_2_executed',
                    'message' => 'There is no subscribers for the event \'command_2_executed\'.',
                    'severity' => 'notice',
                ],
                [
                    'nodeId' => 'execute_command_2_handler',
                    'message' => 'Handler \'execute_command_2_handler\' is triggering the command \'execute_command\'.',
                    'severity' => 'error',
                ],
            ],
        ];
        
        $client = static::createClient();
        $client->request('GET', '/flow/validation');
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals($expectedResponse, $response);
    }
}
