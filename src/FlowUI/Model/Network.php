<?php

namespace FlowUI\Model;

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

    /**
     * @var array
     */
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
            $this->createLink($node, $parent);
            return;
        }

        $this->assignIndex($node);
        $this->createNode($node);
        $this->createLink($node, $parent);

        switch ($node->getType())
        {
            case Node::TYPE_COMMAND:
                if ($this->config['display_handlers']) {
                    $this->addNode($node->getHandler(), $node);
                } else {
                    $this->addNodes($node->getHandler()->getMessages(), $node);
                }
                break;

            case Node::TYPE_EVENT:
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
                break;

            case Node::TYPE_HANDLER:
            case Node::TYPE_SUBSCRIBER:
                $this->addNodes($node->getMessages(), $node);
        }
    }

    /**
     * @param Node $node
     */
    private function createNode(Node $node)
    {
        $this->nodes[$this->getIndex($node)] = [
            "id" => $node->getId(),
            "type" => $node->getType(),
        ];
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function createLink(Node $node, Node $parent = null)
    {
        if ($parent) {
            $this->links[] = [
                "source" => $this->getIndex($parent),
                "target" => $this->getIndex($node),
            ];
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
 