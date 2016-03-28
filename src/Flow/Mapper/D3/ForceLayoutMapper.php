<?php

namespace Flow\Mapper\D3;

use Flow\Network\Network;
use Flow\Network\Node;
use Flow\Mapper\D3\Node as D3Node;

class ForceLayoutMapper
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
    private $indexMap = [];

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $configMap = [
        Node::TYPE_HANDLER    => 'map_handlers',
        Node::TYPE_SUBSCRIBER => 'map_subscribers',
    ];

    /**
     * @param $config
     */
    public function __construct($config)
    {
        // todo: assert array keys
        $this->config = $config;
    }

    /**
     * @param Network $network
     * @return array
     */
    public function map(Network $network)
    {
        $this->init();
        $this->processNodes($network->getNodes());

        return new Layout($this->nodes, $this->links);
    }

    private function init()
    {
        $this->nodes = [];
        $this->links = [];
        $this->indexMap = [];
        $this->count = 0;
    }

    /**
     * @param Node[] $nodes
     * @param Node $parent
     */
    private function processNodes(array $nodes, Node $parent = null)
    {
        foreach ($nodes as $node) {
            $this->processNode($node, $parent);
        }
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function processNode(Node $node, Node $parent = null)
    {
        if ($this->hasIndexAssigned($node)) {
            $this->mapLink($node, $parent);
            return;
        }

        switch ($node->getType())
        {
            case Node::TYPE_COMMAND:
                $this->addNode($node, $parent);
                $this->processNode($node->getHandler(), $node);
                break;

            case Node::TYPE_EVENT:
                $this->addNode($node, $parent);
                $this->processNodes($node->getSubscribers(), $node);
                break;

            case Node::TYPE_HANDLER:
            case Node::TYPE_SUBSCRIBER:
                if ($this->hasMappingEnabled($node)) {
                    $this->addNode($node, $parent);
                    $this->processNodes($node->getMessages(), $node);
                } else {
                    $this->processNodes($node->getMessages(), $parent);
                }
        }
    }

    /**
     * @param Node $node
     * @return boolean
     */
    private function hasMappingEnabled(Node $node)
    {
        if (!isset($this->configMap[$node->getType()])) {
            return true;
        }

        $configKey = $this->configMap[$node->getType()];

        return $this->config[$configKey];
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function addNode(Node $node, Node $parent = null)
    {
        $this->assignIndex($node);
        $this->mapNode($node);
        $this->mapLink($node, $parent);
    }

    /**
     * @param Node $node
     */
    private function mapNode(Node $node)
    {
        $this->nodes[$this->getIndex($node)] = new D3Node($this->getIndex($node), $node->getId(), $node->getType());
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function mapLink(Node $node, Node $parent = null)
    {
        if ($parent) {
            $this->links[] = new Link($this->nodes[$this->getIndex($parent)], $this->nodes[$this->getIndex($node)]);
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

        $this->indexMap[$node->getId()] = $this->count++;
    }

    /**
     * @param Node $node
     * @return int
     * @throws \Exception
     */
    private function getIndex(Node $node)
    {
        if (!$this->hasIndexAssigned($node)) {
            throw new \Exception("Node with index not assigned");
        }

        return $this->indexMap[$node->getId()];
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function hasIndexAssigned(Node $node)
    {
        return isset($this->indexMap[$node->getId()]);
    }
}
 