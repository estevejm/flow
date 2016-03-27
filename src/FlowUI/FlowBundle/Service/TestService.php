<?php

namespace FlowUI\FlowBundle\Service;

use FlowUI\Component\Network\CommandHandlerMap;
use FlowUI\Component\Network\EventSubscribersMap;
use FlowUI\Component\Parser\Parser;
use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;

class TestService
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

    public function build()
    {
        $commands = $this->commandHandlerMap->getCommands();
        $handlers = $this->commandHandlerMap->getHandlers();
        $events = $this->eventSubscribersMap->getEvents();
        $subscribers = $this->eventSubscribersMap->getSubscribers();

        /** @var Command $command */
        foreach ($commands as $command) {
            $handler = $command->getHandler();
            $messages = $this->getMessagesUsedInClass($handler->getClassName());

            foreach ($messages as $messageId) {
                if (!empty($commands[$messageId])) {
                    $handler->addMessage($commands[$messageId]);
                    //var_dump("warning: you're triggering a command inside a command handler!");
                    continue;
                }

                if (empty($events[$messageId])) {
                    $events[$messageId] = new Event($messageId);
                }
                $handler->addMessage($events[$messageId]);
            }
        }

        /** @var Event $event */
        foreach ($events as $event) {
            foreach ($event->getSubscribers() as $subscriber) {
                $messages = $this->getMessagesUsedInClass($subscriber->getClassName());

                foreach ($messages as $messageId) {
                    if (!empty($commands[$messageId])) {
                        $subscriber->addMessage($commands[$messageId]);

                        // if it's a command, it's not an event, moving on ( as we don't want to create an entry for a false event ...
                        continue;
                    }

                    if (empty($events[$messageId])) {
                        // ... here)
                        $events[$messageId] = new Event($messageId);
                    }

                    $subscriber->addMessage($events[$messageId]);
                }
            }

        }

        return array_merge($commands, $handlers, $events, $subscribers);
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMessagesUsedInClass($className) {
        return (new Parser())->parse($className);
    }
}
