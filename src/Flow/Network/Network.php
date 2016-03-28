<?php

namespace Flow\Network;

use Assert\Assertion;
use Flow\Network\Node;

class Network implements NetworkInterface
{
    /**
     * @var Node[]
     */
    private $nodes;

    /**
     * @param Node[] $nodes
     */
    public function __construct(array $nodes)
    {
        Assertion::allIsInstanceOf($nodes, Node::class);

        $this->nodes = $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
