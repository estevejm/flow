<?php

namespace EJM\Flow\Network;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Publisher;

class Blueprint implements NetworkInterface
{
    /**
     * @var Set
     */
    private $commands;

    /**
     * @var Set
     */
    private $events;

    /**
     * @var Set
     */
    private $publishers;

    public function __construct()
    {
        $this->commands = new Set();
        $this->events = new Set();
        $this->publishers = new Set();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes()
    {
        return array_merge(
            $this->commands->toArray(),
            $this->events->toArray(),
            $this->publishers->toArray()
        );
    }

    /**
     * @param Command $command
     * @return $this
     */
    public function addCommand(Command $command)
    {
        if (!$this->commands->has($command->getId())) {
            $this->commands->add($command->getId(), $command);
        }

        return $this;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands->toArray();
    }

    /**
     * @param string $id
     * @return Command
     */
    public function getCommand($id)
    {
        return $this->commands->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasCommand($id)
    {
        return $this->commands->has($id);
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function addEvent(Event $event)
    {
        if (!$this->events->has($event->getId())) {
            $this->events->add($event->getId(), $event);
        }

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events->toArray();
    }

    /**
     * @param string $id
     * @return Event
     */
    public function getEvent($id)
    {
        return $this->events->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasEvent($id)
    {
        return $this->events->has($id);
    }

    /**
     * @param Publisher $messagePublisher
     * @return $this
     */
    public function addPublisher(Publisher $messagePublisher)
    {
        if (!$this->publishers->has($messagePublisher->getId())) {
            $this->publishers->add($messagePublisher->getId(), $messagePublisher);
        }

        return $this;
    }

    /**
     * @return Publisher[]
     */
    public function getPublishers()
    {
        return $this->publishers->toArray();
    }

    /**
     * @param string $id
     * @return Publisher
     */
    public function getPublisher($id)
    {
        return $this->publishers->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasPublisher($id)
    {
        return $this->publishers->has($id);
    }
}
