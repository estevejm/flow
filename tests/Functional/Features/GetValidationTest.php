<?php

namespace EJM\Flow\Tests\Integration\Features;

use EJM\Flow\Collector\MessagesToPublishCollector;
use EJM\Flow\Mapper\D3\ForceLayoutMapper;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Splitter;
use EJM\Flow\Validator\Constraint\EventWithoutSubscriber;
use EJM\Flow\Validator\Constraint\HandlerTriggersCommand;
use EJM\Flow\Validator\Validator;
use PHPUnit_Framework_TestCase;

class GetValidationTest extends PHPUnit_Framework_TestCase
{
    public function testAction()
    {
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

        $builder = new Builder();
        $builder->withAssemblyStage(new AddCommandsAndHandlers($commandHandlerMap));
        $builder->withAssemblyStage(new AddEventsAndSubscribers($eventSubscribersMap));
        $builder->withAssemblyStage(new AddPublishedMessages(new MessagesToPublishCollector()));

        $network = $builder->build();

        $validator = new Validator();
        $validator->addConstraint(new HandlerTriggersCommand());
        $validator->addConstraint(new EventWithoutSubscriber());

        $validation = $validator->validate($network);

        $this->assertEquals($expectedValidation, json_decode(json_encode($validation), true));
    }
}
