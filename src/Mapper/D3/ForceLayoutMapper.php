<?php

namespace EJM\Flow\Mapper\D3;

use Assert\Assertion;
use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node as NetworkNode;

class ForceLayoutMapper
{
    const MAP_HANDLERS = 'map_handlers';
    const MAP_SUBSCRIBERS = 'map_subscribers';

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
        NetworkNode::TYPE_HANDLER    => self::MAP_HANDLERS,
        NetworkNode::TYPE_SUBSCRIBER => self::MAP_SUBSCRIBERS,
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        Assertion::keyExists($config, self::MAP_HANDLERS);
        Assertion::keyExists($config, self::MAP_SUBSCRIBERS);

        $this->config = $config;
    }

    /**
     * @param Network $network
     * @return Layout
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
                    $this->processNodes($node->getMessages(), $node);
                } else {
                    $this->processNodes($node->getMessages(), $parent);
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
        $this->nodes[$this->getIndex($node)] = new Node($this->getIndex($node), $node->getId(), $node->getType());
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
     */
    private function getNode(NetworkNode $node)
    {
        return $this->nodes[$this->getIndex($node)];
    }

    /**
     * @param NetworkNode $node
     */
    private function assignIndex(NetworkNode $node)
    {
        $this->indexMap[$node->getId()] = $this->count++;
    }

    /**
     * @param NetworkNode $node
     * @return int
     */
    private function getIndex(NetworkNode $node)
    {
        return $this->indexMap[$node->getId()];
    }

    /**
     * @param NetworkNode $node
     * @return bool
     */
    private function hasIndexAssigned(NetworkNode $node)
    {
        return isset($this->indexMap[$node->getId()]);
    }
}
 