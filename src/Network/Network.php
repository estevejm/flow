<?php

namespace EJM\Flow\Network;

use Assert\Assertion;
use EJM\Flow\Network\Node;

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
        Assertion::allIsInstanceOf($nodes, '\EJM\Flow\Network\Node');

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
