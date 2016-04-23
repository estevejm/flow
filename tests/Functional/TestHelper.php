<?php

namespace EJM\Flow\Tests\Functional;

use EJM\Flow\Collector\MessagesToPublishCollector;
use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage\AddCommandsAndHandlers;
use EJM\Flow\Network\Builder\AssemblyStage\AddEventsAndSubscribers;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Network;
use EJM\Flow\Validator\Constraint\EventWithoutSubscriber;
use EJM\Flow\Validator\Constraint\HandlerTriggersCommand;
use EJM\Flow\Validator\Validator;

class TestHelper
{
    /**
     * @return Network
     */
    public static function getNetwork()
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

        $builder = new Builder();
        $builder->withAssemblyStage(new AddCommandsAndHandlers($commandHandlerMap));
        $builder->withAssemblyStage(new AddEventsAndSubscribers($eventSubscribersMap));
        $builder->withAssemblyStage(new AddPublishedMessages(new MessagesToPublishCollector()));

        return $builder->build();
    }

    /**
     * @return Validator
     */
    public static function getValidator()
    {
        $validator = new Validator();
        $validator->addConstraint(new HandlerTriggersCommand());
        $validator->addConstraint(new EventWithoutSubscriber());

        return $validator;
    }

    /**
     * @param $object
     * @return array|null
     */
    public static function objectToArray($object)
    {
        return json_decode(json_encode($object), true);
    }
}
 