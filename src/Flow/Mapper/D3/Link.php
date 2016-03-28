<?php

namespace Flow\Mapper\D3;

use JsonSerializable;

class Link implements JsonSerializable
{
    /**
     * @var Node
     */
    private $source;

    /**
     * @var Node
     */
    private $target;

    /**
     * @param Node $source
     * @param Node $target
     */
    public function __construct(Node $source, Node $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * @return Node
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Node
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            "source" => $this->getSource()->getIndex(),
            "target" => $this->getTarget()->getIndex(),
        ];
    }
}
 