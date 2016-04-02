<?php

namespace EJM\Flow\Network\Factory\AssemblyStage;

use Assert;
use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Factory\AssemblyStage;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Handler;

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
        foreach ($commandHandlerMap as $command => $handler) {
            Assert\that($command)->string();
            Assert\that($handler)
                ->isArray()
                ->keyExists('id')
                ->keyExists('class')
                ->all()
                ->string();
        }

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
