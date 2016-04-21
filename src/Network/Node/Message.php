<?php

namespace EJM\Flow\Network\Node;

interface Message 
{
    /**
     * @param MessagePublisher $publisher
     * @return Message
     */
    public function isPublishedBy(MessagePublisher $publisher);

    /**
     * @return MessagePublisher[]
     */
    public function getPublishers();
}
