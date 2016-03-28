<?php

namespace Flow\Network;

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
        // todo: assert all node instance

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
