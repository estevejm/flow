<?php

namespace Flow\Bridge\Symfony\FlowBundle;

use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\RegisterHandlers;
use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\RegisterSubscribers;
use Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\ValidatorConstraintPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlowFlowBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new RegisterHandlers(
                'flow.network.map',
                'command_handler',
                'handles'
            )
        );

        $container->addCompilerPass(
            new RegisterSubscribers(
                'flow.network.map',
                'event_subscriber',
                'subscribes_to'
            )
        );

        $container->addCompilerPass(new ValidatorConstraintPass());
    }
}
