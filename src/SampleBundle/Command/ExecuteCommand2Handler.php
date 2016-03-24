<?php

namespace SampleBundle\Command;

use SampleBundle\Event;
use SampleBundle\Event\Command2Executed;
use SampleBundle\Event\CommandExecuted as AliasedEvent;
use SampleBundle\Event\CommandExecuted;
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
        var_dump("handle -> ", $command);

        $fuck = new Command2Executed($command->getCreatedAt(), 'done: ' . $command->getMessage());
        $this->eventRecorder->record(new Command2Executed($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new \SampleBundle\Event\CommandExecuted($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new Event\CommandExecuted($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record(new AliasedEvent($command->getCreatedAt(), 'done: ' . $command->getMessage()));
        $this->eventRecorder->record($fuck);
        $this->eventRecorder->record($this->getEvent());
        $this->eventRecorder->record(CommandExecuted::create());

    }

    private function getEvent()
    {
        return new Command2Executed(new \DateTime(), 'done');
    }
}
