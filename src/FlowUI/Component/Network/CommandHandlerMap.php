<?php

namespace FlowUI\Component\Network;

use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Handler;

class CommandHandlerMap
{
    /**
     * @var array
     */
    private $callableMap;

    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @var Handler[]
     */
    private $handlers;

    /**
     * @param array $callableMap
     */
    public function __construct(array $callableMap)
    {
        $this->callableMap = $callableMap;
        $this->commands = [];
        $this->handlers = [];

        $this->build();
    }

    private function build()
    {
        foreach ($this->callableMap as $commandId => $handlerData) {
            $command = new Command($commandId);
            $this->handlers[] = new Handler($handlerData['id'], $handlerData['class'], $command);
            $this->commands[$command->getId()] = $command;
        }
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return Handler[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
