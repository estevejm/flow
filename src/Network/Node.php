<?php

namespace EJM\Flow\Network;

use Assert\Assertion;

abstract class Node
{
    const TYPE_COMMAND = 'command';
    const TYPE_HANDLER = 'handler';
    const TYPE_EVENT = 'event';
    const TYPE_SUBSCRIBER = 'subscriber';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $className;

    /**
     * @param string $id
     * @param string $className
     * @param string $type
     */
    public function __construct($id, $className, $type)
    {
        Assertion::string($id);
        Assertion::string($type);

        if (!is_null($className)) {
            Assertion::classExists($className);
        }

        $this->id = $id;
        $this->type = $type;
        $this->className = $className;
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
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
