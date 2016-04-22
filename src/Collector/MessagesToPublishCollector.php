<?php

namespace EJM\Flow\Collector;

use EJM\Flow\Collector\Parser\DataCollectorNodeVisitor;
use EJM\Flow\Collector\Parser\Visitor\MessagesToPublishNodeVisitor;

class MessagesToPublishCollector extends Collector
{
    /**
     * @return DataCollectorNodeVisitor
     */
    protected function getVisitor()
    {
        return new MessagesToPublishNodeVisitor();
    }
}
