<?php

namespace Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidatorConstraintPass implements CompilerPassInterface
{
    const VALIDATOR_SERVICE_ID = 'flow.validator';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::VALIDATOR_SERVICE_ID)) {
            return;
        }

        $definition = $container->findDefinition(self::VALIDATOR_SERVICE_ID);

        foreach ($container->findTaggedServiceIds('flow.validator_constraint') as $serviceId => $tags) {
            $definition->addMethodCall('addConstraint', [$container->findDefinition($serviceId)]);
        }
    }
}
