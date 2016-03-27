<?php

namespace FlowUI\Component\Network;

use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;
use FlowUI\Model\Node\Handler;
use FlowUI\Model\Node\Subscriber;

class Blueprint
{
    /**
     * @var Command[]
     */
    private $commands = [];

    /**
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * @var Subscriber[]
     */
    private $subscribers = [];

    /**
     * @param string $id
     */
    public function addCommand($id)
    {
        $this->commands[$id] = new Command($id);
    }

    /**
     * @param string $id
     * @param string $class
     * @param Command $command
     */
    public function addHandler($id, $class, Command $command)
    {
        $this->handlers[$id] = new Handler($id, $class, $command);
    }

    /**
     * @param string $id
     */
    public function addEvent($id)
    {
        $this->events[$id] = new Event($id);
    }

    /**
     * @param string $id
     * @param string $class
     * @param Event $event
     */
    public function addSubscriber($id, $class, Event $event)
    {
        $this->subscribers[$id] = new Subscriber($id, $class, $event);
    }

    /**
     * @param Handler $handler
     * @param array $messageIds
     */
    public function addHandlerMessages(Handler $handler, array $messageIds)
    {
        foreach ($messageIds as $messageId) {
            if ($this->hasCommand($messageId)) {
                $handler->addMessage($this->getCommand($messageId));
                continue;
            }

            if (!$this->hasEvent($messageId)) {
                $this->addEvent($messageId);
            }

            $handler->addMessage($this->getEvent($messageId));
        }
    }

    /**
     * @param Subscriber $subscriber
     * @param array $messageIds
     */
    public function addSubscriberMessages(Subscriber $subscriber, array $messageIds)
    {
        foreach ($messageIds as $messageId) {
            if ($this->hasCommand($messageId)) {
                $subscriber->addMessage($this->getCommand($messageId));
                continue;
            }

            if (!$this->hasEvent($messageId)) {
                $this->addEvent($messageId);
            }

            $subscriber->addMessage($this->getEvent($messageId));
        }
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
    public function hasCommand($id)
    {
        return isset($this->commands[$id]);
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
     * @param string $class
     * @param string $id
     * @throws \Exception
     */
    private function throwNotFoundException($class, $id)
    {
        throw new \Exception(sprintf('Node of type %s with id \'%s\' not found', $class, $id));
    }

    /**
     * @return \FlowUI\Model\Node\Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return \FlowUI\Model\Node\Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return \FlowUI\Model\Node\Handler[]
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * @return \FlowUI\Model\Node\Subscriber[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }
}
