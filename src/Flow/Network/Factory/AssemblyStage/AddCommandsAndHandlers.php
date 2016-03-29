<?php

namespace Flow\Network\Factory\AssemblyStage;

use Flow\Network\Blueprint;
use Flow\Network\Factory\AssemblyStage;
use Flow\Network\Node\Command;
use Flow\Network\Node\Handler;

class AddCommandsAndHandlers implements AssemblyStage
{
    /**
     * @var array
     */
    private $commandHandlerMap;

    /**
     * @param array $commandHandlerMap
     */
    public function __construct(array $commandHandlerMap)
    {
        // todo: assert map format
        $this->commandHandlerMap = $commandHandlerMap;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach ($this->commandHandlerMap as $commandId => $handlerData) {
            $blueprint->addCommand(new Command($commandId));
            $blueprint->addMessagePublisher(
                new Handler($handlerData['id'], $handlerData['class'], $blueprint->getCommand($commandId))
            );
        }
    }
}
