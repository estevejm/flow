<?php

namespace FlowUI\Model\Node;

interface MessagePublisher
{
    /**
     * @return Message[]
     */
    public function getMessages();

    /**
     * @param Message $message
     */
    public function addMessage(Message $message);
}
