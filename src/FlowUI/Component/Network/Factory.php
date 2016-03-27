<?php

namespace FlowUI\Component\Network;

use FlowUI\Component\Parser\Parser;

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
        $blueprint = new Blueprint();
        
        foreach ($map->getCommandHandlerMap() as $commandId => $handlerData) {
            $blueprint->addCommand($commandId);
            $blueprint->addHandler($handlerData['id'], $handlerData['class'], $blueprint->getCommand($commandId));
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $eventSubscribers) {
            $blueprint->addEvent($eventId);
            foreach ($eventSubscribers as $subscriber) {
                $blueprint->addSubscriber($subscriber['id'], $subscriber['class'], $blueprint->getEvent($eventId));
            }
        }

        foreach ($blueprint->getHandlers() as $handler) {
            $messageIds = $this->getIdsOfMessagesInClass($handler->getClassName());
            $blueprint->addHandlerMessages($handler, $messageIds);
        }

        foreach ($blueprint->getSubscribers() as $subscriber) {
            $messageIds = $this->getIdsOfMessagesInClass($subscriber->getClassName());
            $blueprint->addSubscriberMessages($subscriber, $messageIds);
        }

        return new Network(
            $blueprint->getCommands(),
            $blueprint->getHandlers(),
            $blueprint->getEvents(),
            $blueprint->getSubscribers()
        );
    }

    /**
     * @param string $className
     * @return array
     */
    private function getIdsOfMessagesInClass($className) {
        return $this->parser->parse($className);
    }
}
