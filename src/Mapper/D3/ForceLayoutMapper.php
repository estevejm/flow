<?php

namespace EJM\Flow\Mapper\D3;

use Assert\Assertion;
use EJM\Flow\Common\Set;
use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node as NetworkNode;

class ForceLayoutMapper
{
    const MAP_HANDLERS = 'map_handlers';
    const MAP_SUBSCRIBERS = 'map_subscribers';

    /**
     * @var Set
     */
    private $indexMap;

    /**
     * @var Set
     */
    private $nodes;

    /**
     * @var array
     */
    private $links;

    /**
     * @var int
     */
    private $count;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $configMap = [
        NetworkNode::TYPE_HANDLER    => self::MAP_HANDLERS,
        NetworkNode::TYPE_SUBSCRIBER => self::MAP_SUBSCRIBERS,
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
    }

    /**
     * @param Network $network
     * @return Layout
     */
    public function map(Network $network)
    {
        $this->init();
        $this->processNodes($network->getNodes());

        return new Layout($this->nodes->toArray(), $this->links);
    }

    private function init()
    {
        $this->indexMap = new Set();
        $this->nodes = new Set();
        $this->links = [];
        $this->count = 0;
    }

    /**
     * @param NetworkNode[] $nodes
     * @param NetworkNode $parent
     */
    private function processNodes(array $nodes, NetworkNode $parent = null)
    {
        foreach ($nodes as $node) {
            $this->processNode($node, $parent);
        }
    }

    /**
     * @param NetworkNode $node
     * @param NetworkNode $parent
     */
    private function processNode(NetworkNode $node, NetworkNode $parent = null)
    {
        if ($this->hasIndexAssigned($node)) {
            $this->mapLink($node, $parent);
            return;
        }

        switch ($node->getType())
        {
            case NetworkNode::TYPE_COMMAND:
                $this->addNode($node, $parent);
                $this->processNode($node->getHandler(), $node);
                break;

            case NetworkNode::TYPE_EVENT:
                $this->addNode($node, $parent);
                $this->processNodes($node->getSubscribers(), $node);
                break;

            case NetworkNode::TYPE_HANDLER:
            case NetworkNode::TYPE_SUBSCRIBER:
                if ($this->hasMappingEnabled($node)) {
                    $this->addNode($node, $parent);
                    $this->processNodes($node->getMessagesToPublish(), $node);
                } else {
                    $this->processNodes($node->getMessagesToPublish(), $parent);
                }
        }
    }

    /**
     * @param NetworkNode $node
     * @return boolean
     */
    private function hasMappingEnabled(NetworkNode $node)
    {
        $configKey = $this->configMap[$node->getType()];

        return $this->config[$configKey];
    }

    /**
     * @param NetworkNode $node
     * @param NetworkNode $parent
     */
    private function addNode(NetworkNode $node, NetworkNode $parent = null)
    {
        $this->assignIndex($node);
        $this->mapNode($node);
        $this->mapLink($node, $parent);
    }

    /**
     * @param NetworkNode $node
     */
    private function mapNode(NetworkNode $node)
    {
        $index = $this->getIndex($node);
        $node = new Node($this->getIndex($node), $node->getId(), $node->getType());

        $this->nodes->add($index, $node);
    }

    /**
     * @param NetworkNode $node
     * @param NetworkNode $parent
     */
    private function mapLink(NetworkNode $node, NetworkNode $parent = null)
    {
        if ($parent) {
            $this->links[] = new Link($this->getNode($parent), $this->getNode($node));
        }
    }

    /**
     * @param NetworkNode $node
     * @return Node
     */
    private function getNode(NetworkNode $node)
    {
        return $this->nodes->get($this->getIndex($node));
    }

    /**
     * @param NetworkNode $node
     */
    private function assignIndex(NetworkNode $node)
    {
        $this->indexMap->add($node->getId(), $this->count++);
    }

    /**
     * @param NetworkNode $node
     * @return int
     */
    private function getIndex(NetworkNode $node)
    {
        return $this->indexMap->get($node->getId());
    }

    /**
     * @param NetworkNode $node
     * @return bool
     */
    private function hasIndexAssigned(NetworkNode $node)
    {
        return $this->indexMap->has($node->getId());
    }

    /**
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            ForceLayoutMapper::MAP_HANDLERS => true,
            ForceLayoutMapper::MAP_SUBSCRIBERS => true,
        ];
    }
}
 