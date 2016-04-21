<?php

namespace EJM\Flow\Network;

class Splitter
{
    /**
     * @var array
     */
    private $processed;

    /**
     * @var Blueprint[]
     */
    private $blueprints;

    /**
     * @param Network $network
     * @return Network[]
     */
    public function split(Network $network)
    {
        $this->processed = [];
        $this->blueprints = [];
        $current = 0;

        foreach ($network->getNodes() as $node) {
            if (!isset($this->processed[$node->getId()])) {
                $this->blueprints[$current] = new Blueprint();
                $this->processNode($current, $node);
                $current++;
            }
        }

        return array_map([$this, 'buildNetwork'], $this->blueprints);
    }

    /**
     * @param int $current
     * @param array $nodes
     */
    private function processNodes($current, array $nodes)
    {
        foreach ($nodes as $node) {
            $this->processNode($current, $node);
        }
    }

    /**
     * @param int $current
     * @param Node $node
     */
    private function processNode($current, Node $node = null)
    {
        if (isset($this->processed[$node->getId()])) {
            return;
        }

        $this->processed[$node->getId()] = $current;

        switch ($node->getType()) {
            case Node::TYPE_COMMAND:
                $this->blueprints[$current]->addCommand($node);

                $this->processNodes($current, $node->getPublishers());
                $this->processNode($current, $node->getHandler());
                break;

            case Node::TYPE_EVENT:
                $this->blueprints[$current]->addEvent($node);

                $this->processNodes($current, $node->getPublishers());
                $this->processNodes($current, $node->getSubscribers());
                break;

            case Node::TYPE_HANDLER:
                $this->blueprints[$current]->addMessagePublisher($node);

                $this->processNodes($current, $node->getCommandsToHandle());
                $this->processNodes($current, $node->getMessages());
                break;

            case Node::TYPE_SUBSCRIBER:
            $this->blueprints[$current]->addMessagePublisher($node);

            $this->processNodes($current, $node->getEventsSubscribedTo());
            $this->processNodes($current, $node->getMessages());
        }
    }

    /**
     * @param Blueprint $blueprint
     * @return Network
     */
    private function buildNetwork(Blueprint $blueprint)
    {
        return new Network($blueprint->getNodes());
    }
}
 