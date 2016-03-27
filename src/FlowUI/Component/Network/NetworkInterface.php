<?php

namespace FlowUI\Component\Network;

use FlowUI\Model\Node;

interface NetworkInterface
{
    /**
     * @return Node[]
     */
    public function getNodes();
}
