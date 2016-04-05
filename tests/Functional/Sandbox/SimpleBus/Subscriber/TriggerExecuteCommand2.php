<?php

namespace EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Subscriber;

use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Command\ExecuteCommand2;
use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\Command2Executed;
use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\CommandExecuted;

class TriggerExecuteCommand2
{
    /**
     * @param CommandExecuted $event
     */
    public function __invoke(CommandExecuted $event)
    {
        new ExecuteCommand2($event->getCreatedAt(), $event->getMessage());
        new Command2Executed($event->getCreatedAt(), $event->getMessage());
    }
}
