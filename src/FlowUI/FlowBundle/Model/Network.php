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
    private $map = [];

    /**
     * @var int
     */
    private $count = 0;

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
        if (isset($this->map[$node->getId()])) {
            return;
        }

        $this->assignIndex($node);

        $this->nodes[$this->map[$node->getId()]] = [
            "name" => $node->getId(),
            "group" => $node->getType(),
        ];

        if ($parent) {
            $this->links[] = [
                "source" => $this->getIndex($parent),
                "target" => $this->getIndex($node),
                "value" => 1,
            ];
        }

        if ($node instanceof Command) {
            $this->addNode($node->getHandler(), $node);
        } elseif ($node instanceof Event) {
            $this->addNodes($node->getSubscribers(), $node);
        } elseif ($node instanceof Handler || $node instanceof Subscriber) {
            $this->addNodes($node->getMessages(), $node);
        }
    }

    private function assignIndex(Node $node)
    {
        if (isset($this->map[$node->getId()])) {
            throw new \Exception("Node with index already assigned");
        }

        $this->map[$node->getId()] = $this->count++;
    }

    private function getIndex(Node $node)
    {
        return $this->map[$node->getId()];
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
 