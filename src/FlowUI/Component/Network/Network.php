<?php

namespace FlowUI\Component\Network;

use FlowUI\Model\Node;
use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;
use FlowUI\Model\Node\Handler;
use FlowUI\Model\Node\Subscriber;

class Network
{
    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @var Handler[]
     */
    private $handlers;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @var Subscriber[]
     */
    private $subscribers;

    /**
     * @param Command[] $commands
     * @param Handler[] $handlers
     * @param Event[] $events
     * @param Subscriber[] $subscribers
     */
    public function __construct(array $commands, array $handlers, array $events, array $subscribers)
    {
        // todo: assert all of proper instance

        $this->commands = $commands;
        $this->handlers = $handlers;
        $this->events = $events;
        $this->subscribers = $subscribers;
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

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return array_merge($this->getCommands(), $this->getHandlers(), $this->getEvents(), $this->getSubscribers());
    }
}
