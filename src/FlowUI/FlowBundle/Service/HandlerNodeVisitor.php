<?php

namespace FlowUI\FlowBundle\Service;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class HandlerNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $uses;

    public function __construct()
    {
        $this->uses = [];
    }


    public function leaveNode(Node $node) {
        if ($node instanceof Node\Stmt\Use_) {
            $this->uses[] = $node->uses[0]->name->toString();
        }
    }

    /**
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }
}
