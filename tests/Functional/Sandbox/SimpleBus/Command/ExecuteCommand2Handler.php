<?php

namespace EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Command;

use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event;
use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\Command2Executed;
use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\CommandExecuted as AliasedEvent;
use EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\CommandExecuted;
use SimpleBus\Message\Recorder\PublicMessageRecorder;

class ExecuteCommand2Handler
{
    /**
     * @var PublicMessageRecorder
     */
    private $eventRecorder;

    /**
     * @param PublicMessageRecorder $eventRecorder
     */
    public function __construct(PublicMessageRecorder $eventRecorder)
    {
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param ExecuteCommand2 $command
     */
    public function __invoke(ExecuteCommand2 $command)
    {
        $fuck = new Command2Executed($command->getCreatedAt(), 'done: ' . $command->getMessage());
        $this->eventRecorder->record(new Command2Executed($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new \EJM\Flow\Tests\Functional\Sandbox\SimpleBus\Event\CommandExecuted($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new Event\CommandExecuted($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new AliasedEvent($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record($fuck);
        $this->eventRecorder->record($this->getEvent());
        $this->eventRecorder->record(CommandExecuted::create());

        new ExecuteCommand(new \DateTime(), 'not a best practice');
    }

    private function getEvent()
    {
        return new Command2Executed(new \DateTime(), 'done');
    }
}
