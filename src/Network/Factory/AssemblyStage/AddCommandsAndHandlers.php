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
                ->keyExists('class');
        }

        $this->commandHandlerMap = $commandHandlerMap;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach ($this->commandHandlerMap as $commandId => $handlerData) {
            $handler = $this->findOrCreateHandler($blueprint, $handlerData);
            $command = new Command($commandId, $handler);

            $blueprint->addCommand($command);
            $blueprint->addPublisher($handler);
        }
    }

    /**
     * @param Blueprint $blueprint
     * @param array $handlerData
     * @return Handler
     */
    private function findOrCreateHandler(Blueprint $blueprint, array $handlerData)
    {
        if ($blueprint->hasPublisher($handlerData['id'])) {
            return $blueprint->getPublisher($handlerData['id']);
        }

        return new Handler($handlerData['id'], $handlerData['class']);
    }
}
