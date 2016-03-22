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
                'test_service',
                'command_handler',
                'handles'
            )
        );

        $container->addCompilerPass(
            new RegisterSubscribers(
                'test_service',
                'event_subscriber',
                'subscribes_to'
            )
        );
    }
}
