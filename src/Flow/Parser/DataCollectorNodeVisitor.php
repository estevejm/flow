<?php

namespace Flow\Parser;

use PhpParser\NodeVisitorAbstract;

abstract class DataCollectorNodeVisitor extends NodeVisitorAbstract
{
    public abstract function getData();
}
