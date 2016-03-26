<?php

namespace FlowUI\Component\Validator;

use FlowUI\FlowBundle\Model\Node;

class Violation
{
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';

    /**
     * @var Node
     */
    private $node;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $severity;

    /**
     * @param Node $node
     * @param string $message
     * @param string $severity
     */
    public function __construct(Node $node, $message, $severity = self::ERROR)
    {
        $this->message = $message;
        $this->severity = $severity;
        $this->node = $node;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}
 