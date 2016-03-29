<?php

namespace EJM\Flow\Collector\Parser;

use PhpParser\NodeVisitorAbstract;

abstract class DataCollectorNodeVisitor extends NodeVisitorAbstract
{
    public abstract function getData();
}
