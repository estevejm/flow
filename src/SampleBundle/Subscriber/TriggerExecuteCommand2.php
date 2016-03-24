<?php

namespace SampleBundle\Subscriber;

use SampleBundle\Command\ExecuteCommand2;
use SampleBundle\Event\Command2Executed;
use SampleBundle\Event\CommandExecuted;

class TriggerExecuteCommand2
{

    /**
     * @param CommandExecuted $event
     */
    public function __invoke(CommandExecuted $event)
    {
        var_dump("command was executed -> ", $event);

        new ExecuteCommand2($event->getCreatedAt(), $event->getMessage());
        new Command2Executed($event->getCreatedAt(), $event->getMessage());
    }
}
