<?php

namespace SampleBundle\Subscriber;

use SampleBundle\Event\CommandExecuted;

class LogCommandExecuted
{
    /**
     * @param CommandExecuted $event
     */
    public function __invoke(CommandExecuted $event)
    {
        var_dump("command was executed -> ", $event);
    }
}
