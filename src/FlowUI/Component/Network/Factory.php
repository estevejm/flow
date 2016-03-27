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
        $blueprint = new Blueprint();

        // todo: move $publisherMessagesMap build process to compiler pass

        $publisherMessagesMap = [];
        foreach ($map->getCommandHandlerMap() as $commandId => $handlerData) {
            $blueprint->addCommand(new Command($commandId));
            $blueprint->addMessagePublisher(
                new Handler($handlerData['id'], $handlerData['class'], $blueprint->getCommand($commandId))
            );

            $publisherMessagesMap[$handlerData['id']] = $this->getIdsOfMessagesInClass($handlerData['class']);
        }

        foreach ($map->getEventSubscribersMap() as $eventId => $eventSubscribers) {
            $blueprint->addEvent(new Event($eventId));
            foreach ($eventSubscribers as $subscriberData) {
                $blueprint->addMessagePublisher(
                    new Subscriber($subscriberData['id'], $subscriberData['class'], $blueprint->getEvent($eventId))
                );

                $publisherMessagesMap[$subscriberData['id']] = $this->getIdsOfMessagesInClass($subscriberData['class']);
            }
        }

        foreach($publisherMessagesMap as $messagePublisherId => $messageIds) {
            $messagePublisher = $blueprint->getMessagePublisher($messagePublisherId);
            foreach ($messageIds as $messageId) {
                if ($blueprint->hasCommand($messageId)) {
                    $messagePublisher->addMessage($blueprint->getCommand($messageId));
                    continue;
                }

                if (!$blueprint->hasEvent($messageId)) {
                    $blueprint->addEvent(new Event($messageId));
                }

                $messagePublisher->addMessage($blueprint->getEvent($messageId));
            }

        }

        return new Network($blueprint->getNodes());
    }

    /**
     * @param string $className
     * @return array
     */
    private function getIdsOfMessagesInClass($className) {
        return $this->parser->parse($className);
    }
}
