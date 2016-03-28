<?php

namespace Flow\Network;

class Map
{
    /**
     * @var array
     */
    private $commandHandlerMap;

    /**
     * @var array
     */
    private $eventSubscribersMap;

    /**
     * @param array $commandHandlerMap
     * @param array $eventSubscribersMap
     */
    public function __construct(array $commandHandlerMap, array $eventSubscribersMap)
    {
        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventSubscribersMap = $eventSubscribersMap;
    }

    /**
     * @return array
     */
    public function getCommandHandlerMap()
    {
        return $this->commandHandlerMap;
    }

    /**
     * @return array
     */
    public function getEventSubscribersMap()
    {
        return $this->eventSubscribersMap;
    }
}
