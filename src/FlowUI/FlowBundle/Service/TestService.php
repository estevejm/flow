<?php

namespace FlowUI\FlowBundle\Service;

use Exception;
use FlowUI\FlowBundle\Model\Command;
use FlowUI\FlowBundle\Model\Event;
use FlowUI\FlowBundle\Model\Handler;
use FlowUI\FlowBundle\Model\Subscriber;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionClass;
use SimpleBus\Message\Name\NamedMessage;

class TestService
{
    /**
     * @var array
     */
    private $handlersMap;

    /**
     * @var array
     */
    private $subscribersMap;

    /**
     * @param array $handlersMap
     * @param array $subscribersMap
     */
    public function __construct(array $handlersMap, array $subscribersMap)
    {
        $this->handlersMap = $handlersMap;
        $this->subscribersMap = $subscribersMap;
    }

    public function build()
    {
        $commands = $events = [];

        foreach ($this->handlersMap as $commandId => $handlerData) {
            $command = new Command($commandId);
            new Handler($handlerData['id'], $handlerData['class'], $command);
            $commands[$command->getId()] = $command;
        }

        foreach ($this->subscribersMap as $eventId => $subscriberIds) {
            $event = new Event($eventId);

            array_map(
                function($subscriberId) use ($event) {
                    return new Subscriber($subscriberId, $event);
                },
                $subscriberIds
            );

            $events[$event->getId()] = $event;
        }

        /** @var Command $command */
        foreach ($commands as $command) {
            $handler = $command->getHandler();
            $handlerEventIds = $this->getEventsTriggeredByHandler($handler);

            foreach ($handlerEventIds as $handlerEventId) {
                if (empty($events[$handlerEventId])) {
                    $events[$handlerEventId] = new Event($handlerEventId);
                }
                $handler->addEvent($events[$handlerEventId]);
            }
        }

        return array_merge($commands, $events);
    }

    /**
     * @param Handler $handler
     * @return array
     */
    private function getEventsTriggeredByHandler(Handler $handler) {
        $class = new ReflectionClass($handler->getClassName());
        $fileName = $class->getFileName();
        $code = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $visitor = new RecordedEventsNodeVisitor();
        $traverser->addVisitor($visitor);

        try {
            $stmts = $parser->parse($code);
            $stmts = $traverser->traverse($stmts);

            //var_dump($stmts);
            return $visitor->getEventIds();


        } catch (Exception $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        return [];
    }
}
