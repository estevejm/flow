<?php

namespace FlowUI\FlowBundle\Model;

class Network
{
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var array
     */
    private $links = [];

    /**
     * @var array
     */
    private $index = [];

    /**
     * @var int
     */
    private $count = 0;

    private $config = [
        'display_handlers' => true,
        'display_subscribers' => true,
    ];
    /**
     * @param Node[] $nodes
     */
    public function __construct(array $nodes)
    {
        $this->addNodes($nodes);
    }

    /**
     * @param Node[] $nodes
     * @param Node $parent
     */
    private function addNodes(array $nodes, Node $parent = null) {
        foreach($nodes as $node) {
            $this->addNode($node, $parent);
        }
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function addNode(Node $node, Node $parent = null)
    {
        if ($this->hasIndexAssigned($node)) {
            return;
        }

        $this->assignIndex($node);

        $this->nodes[$this->index[$node->getId()]] = [
            "name" => $node->getId(),
            "group" => $node->getType(),
        ];

        if ($parent) {
            $this->links[] = [
                "source" => $this->getIndex($parent),
                "target" => $this->getIndex($node),
            ];
        }

        if ($node instanceof Command) {
            if ($this->config['display_handlers']) {
                $this->addNode($node->getHandler(), $node);
            } else {
                $this->addNodes($node->getHandler()->getMessages(), $node);
            }
        } elseif ($node instanceof Event) {
            if ($this->config['display_subscribers']) {
                $this->addNodes($node->getSubscribers(), $node);
            } else {
                array_map(
                    function($subscriber) use ($node) {
                        $this->addNodes($subscriber->getMessages(), $node);
                    },
                    $node->getSubscribers()
                );
            }
        } elseif ($node instanceof Handler || $node instanceof Subscriber) {
            $this->addNodes($node->getMessages(), $node);
        }
    }

    /**
     * @param Node $node
     * @throws \Exception
     */
    private function assignIndex(Node $node)
    {
        if ($this->hasIndexAssigned($node)) {
            throw new \Exception("Node with index already assigned");
        }

        $this->index[$node->getId()] = $this->count++;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function hasIndexAssigned(Node $node)
    {
        return isset($this->index[$node->getId()]);
    }

    /**
     * @param Node $node
     * @return int
     */
    private function getIndex(Node $node)
    {
        return $this->index[$node->getId()];
    }

    /**
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}
 