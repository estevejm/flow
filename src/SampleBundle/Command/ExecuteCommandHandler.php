<?php

namespace SampleBundle\Command;

use SampleBundle\Event\CommandExecuted;
use SimpleBus\Message\Recorder\PublicMessageRecorder;

class ExecuteCommandHandler
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
     * @param ExecuteCommand $command
     */
    public function __invoke(ExecuteCommand $command)
    {
        var_dump("handle -> ", $command);

        $this->eventRecorder->record(new CommandExecuted($command->getCreatedAt(), 'done: ' . $command->getMessage()));
    }
}
