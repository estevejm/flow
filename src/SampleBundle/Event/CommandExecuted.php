<?php

namespace SampleBundle\Event;

use SimpleBus\Message\Name\NamedMessage;

class CommandExecuted implements NamedMessage
{

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $message;

    /**
     * @param \DateTime $createdAt
     * @param string $message
     */
    public function __construct(\DateTime $createdAt, $message)
    {
        $this->createdAt = $createdAt;
        $this->message = $message;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * The name of this particular type of message.
     *
     * @return string
     */
    public static function messageName()
    {
        return 'command_executed';
    }
}
 