<?php

namespace FlowUI\Component\Network;

use FlowUI\Component\Parser\Parser;
use FlowUI\Model\Node\Command;
use FlowUI\Model\Node\Event;
use FlowUI\Model\Node\Handler;
use FlowUI\Model\Node\Subscriber;

class Factory
{
    /**
     * @var $parser
     */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param Map $map
     * @return Network
     */
    public function create(Map $map)
    {
        $commands = [];
        $handlers = [];
        $events = [];
        $subscribers = [];

        foreach ($map->getCommandHandlerMap() as $commandId => $handlerData) {
            $command = new Command($commandId);
            $handlers[$handlerData['id']] = new Handler($handlerData['id'], $handlerData['class'], $command);
            $commands[$command->getId()] = $command;
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $eventSubscribers) {
            $event = new Event($eventId);

            foreach ($eventSubscribers as $subscriber) {
                $subscribers[$subscriber['id']] = new Subscriber($subscriber['id'], $subscriber['class'], $event);
            }

            $events[$event->getId()] = $event;
        }

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
        return $this->parser->parse($className);
    }
}
