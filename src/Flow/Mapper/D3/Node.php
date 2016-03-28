<?php

namespace Flow\Mapper\D3;

use JsonSerializable;

class Node implements JsonSerializable
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @param int $index
     * @param string $id
     * @param string $type
     */
    public function __construct($index, $id, $type)
    {
        // todo: assertions
        $this->index = $index;
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "type" => $this->getType(),
        ];
    }
}
