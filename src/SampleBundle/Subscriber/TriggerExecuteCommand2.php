<?php

namespace SampleBundle\Subscriber;

use SampleBundle\Event\CommandExecuted;

class TriggerExecuteCommand2
{
    /**
     * @param CommandExecuted $event
     */
    public function __invoke(CommandExecuted $event)
    {
        var_dump("command was executed -> ", $event);
    }
}
