<?php

namespace EJM\Flow\Tests\Functional\Features;

use EJM\Flow\Collector\MessagesToPublishCollector;
use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Splitter;
use EJM\Flow\Tests\Functional\TestHelper;
use EJM\Flow\Validator\Constraint\EventWithoutSubscriber;
use EJM\Flow\Validator\Constraint\HandlerTriggersCommand;
use EJM\Flow\Validator\Validator;
use PHPUnit_Framework_TestCase;

class GetValidationTest extends PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $expectedValidation = [
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

        $network = TestHelper::getNetwork();
        $validator = TestHelper::getValidator();

        $validation = $validator->validate($network);

        $this->assertEquals($expectedValidation, TestHelper::objectToArray($validation));
    }
}
