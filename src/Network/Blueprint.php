<?php

namespace EJM\Flow\Network;

use EJM\Flow\Network\Node;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\MessagePublisher;

class Blueprint implements NetworkInterface
{
    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @var MessagePublisher[]
     */
    private $messagePublishers;

    /**
     * {@inheritdoc}
     */
    public function getNodes()
    {
        return array_merge($this->commands, $this->events, $this->messagePublishers);
    }

    /**
     * @param Command $command
     */
    public function addCommand(Command $command)
    {
        $this->commands[$command->getId()] = $command;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Command
     */
    public function getCommand($id)
    {
        if (!$this->hasCommand($id))
        {
            $this->throwNotFoundException(Command::class, $id);
        }

        return $this->commands[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasCommand($id)
    {
        return isset($this->commands[$id]);
    }

    /**
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events[$event->getId()] = $event;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return Event
     */
    public function getEvent($id)
    {
        if (!$this->hasEvent($id))
        {
            $this->throwNotFoundException(Event::class, $id);
        }

        return $this->events[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasEvent($id)
    {
        return isset($this->events[$id]);
    }

    /**
     * @param MessagePublisher $messagePublisher
     */
    public function addMessagePublisher(MessagePublisher $messagePublisher)
    {
        $this->messagePublishers[$messagePublisher->getId()] = $messagePublisher;
    }

    /**
     * @return MessagePublisher[]
     */
    public function getMessagePublishers()
    {
        return $this->messagePublishers;
    }

    /**
     * @param string $id
     * @throws \Exception
     * @return MessagePublisher
     */
    public function getMessagePublisher($id)
    {
        if (!$this->hasMessagePublisher($id))
        {
            $this->throwNotFoundException(MessagePublisher::class, $id);
        }

        return $this->messagePublishers[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasMessagePublisher($id)
    {
        return isset($this->messagePublishers[$id]);
    }

    /**
     * @param string $class
     * @param string $id
     * @throws \Exception
     */
    private function throwNotFoundException($class, $id)
    {
        throw new \Exception(sprintf('Node of type %s with id \'%s\' not found', $class, $id));
    }
}
