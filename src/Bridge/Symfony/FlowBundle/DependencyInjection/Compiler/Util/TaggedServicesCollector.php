<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\DependencyInjection\Compiler\Util;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait TaggedServicesCollector
{
    /**
     * @param ContainerBuilder $container
     * @param string $tagName
     * @param string $keyAttribute
     * @param callable $callback
     */
    private function collect(ContainerBuilder $container, $tagName, $keyAttribute, callable $callback)
    {
        foreach ($container->findTaggedServiceIds($tagName) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes[$keyAttribute])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The attribute "%s" of tag "%s" of service "%s" is mandatory',
                            $keyAttribute,
                            $tagName,
                            $serviceId
                        )
                    );
                }

                $key = $tagAttributes[$keyAttribute];
                $definition = $container->findDefinition($serviceId);

                call_user_func($callback, ltrim($key, '\\'), $serviceId, $definition);
            }
        }
    }
}
