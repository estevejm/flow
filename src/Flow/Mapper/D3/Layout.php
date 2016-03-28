<?php

namespace Flow\Mapper\D3;

use Assert\Assertion;
use JsonSerializable;

class Layout implements JsonSerializable
{
    /**
     * @var Node[]
     */
    private $nodes;

    /**
     * @var Link[]
     */
    private $links;

    /**
     * @param Node[] $nodes
     * @param Link[] $links
     */
    public function __construct(array $nodes, array $links)
    {
        Assertion::allIsInstanceOf($nodes, Node::class);
        Assertion::allIsInstanceOf($links, Link::class);

        $this->nodes = $nodes;
        $this->links = $links;
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            'nodes' => $this->getNodes(),
            'links' => $this->getLinks(),
        ];
    }
}
