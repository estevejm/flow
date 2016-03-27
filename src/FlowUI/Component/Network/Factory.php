<?php

namespace FlowUI\Component\Network;

use FlowUI\Component\Parser\Parser;
use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;

class Factory
{
    /**
     * @var CommandHandlerMap
     */
    private $commandHandlerMap;

    /**
     * @var EventSubscribersMap
     */
    private $eventSubscribersMap;

    /**
     * @param CommandHandlerMap $commandHandlerMap
     * @param EventSubscribersMap $eventSubscribersMap
     */
    public function __construct(CommandHandlerMap $commandHandlerMap, EventSubscribersMap $eventSubscribersMap)
    {
        $this->commandHandlerMap = $commandHandlerMap;
        $this->eventSubscribersMap = $eventSubscribersMap;
    }

    /**
     * @return Network
     */
    public function create()
    {
        $commands = $this->commandHandlerMap->getCommands();
        $handlers = $this->commandHandlerMap->getHandlers();
        $events = $this->eventSubscribersMap->getEvents();
        $subscribers = $this->eventSubscribersMap->getSubscribers();

        foreach ($handlers as $handler) {
            $messages = $this->getMessagesUsedInClass($handler->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $handler->addMessage($commands[$messageId]);
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }

                $handler->addMessage($events[$messageId]);
            }
        }

        foreach ($subscribers as $subscriber) {
            $messages = $this->getMessagesUsedInClass($subscriber->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $subscriber->addMessage($commands[$messageId]);
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }

                $subscriber->addMessage($events[$messageId]);
            }
        }

        return new Network($commands, $handlers, $events, $subscribers);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMessagesUsedInClass($className) {
        return (new Parser())->parse($className);
    }
}
