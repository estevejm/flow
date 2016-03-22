<?php

namespace FlowUI\FlowBundle\Service;

use FlowUI\FlowBundle\Model\Command;
use FlowUI\FlowBundle\Model\Event;
use FlowUI\FlowBundle\Model\Handler;
use FlowUI\FlowBundle\Model\Subscriber;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;

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

    /**
     * @param string $name
     * @return callable
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->handlersMap)) {
            throw new UndefinedCallable(
                sprintf(
                    'Could not find a callable for name "%s"',
                    $name
                )
            );
        }

        return $this->handlersMap[$name];
    }

    public function build()
    {
        $tree = [];

        foreach ($this->handlersMap as $commandId => $handlerId) {
            $command = new Command($commandId);
            $handler = new Handler($handlerId, $command);
            $tree[$command->getId()] = $command;
        }

        foreach ($this->subscribersMap as $eventId => $subscriberIds) {
            $event = new Event($eventId);

            array_map(
                function($subscriberId) use ($event) {
                    return new Subscriber($subscriberId, $event);
                },
                $subscriberIds
            );

            $tree[$event->getId()] = $event;
        }
        var_dump($tree);
    }
}
