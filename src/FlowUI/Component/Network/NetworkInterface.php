<?php

namespace FlowUI\Component\Network;

use FlowUI\Component\Network\Node;

interface NetworkInterface
{
    /**
     * @return Node[]
     */
    public function getNodes();
}
