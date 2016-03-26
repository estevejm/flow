<?php

namespace FlowUI\FlowBundle;

use FlowUI\FlowBundle\DependencyInjection\Compiler\RegisterHandlers;
use FlowUI\FlowBundle\DependencyInjection\Compiler\RegisterSubscribers;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlowUIFlowBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new RegisterHandlers(
                'flow.network.command_handler_map',
                'command_handler',
                'handles'
            )
        );

        $container->addCompilerPass(
            new RegisterSubscribers(
                'flow.network.event_subscribers_map',
                'event_subscriber',
                'subscribes_to'
            )
        );
    }
}
