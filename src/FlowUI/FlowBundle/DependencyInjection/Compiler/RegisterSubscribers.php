<?php

namespace FlowUI\FlowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterSubscribers implements CompilerPassInterface
{
    private $serviceId;
    private $tag;
    private $keyAttribute;

    /**
     * @param string  $serviceId            The service id of the MessageSubscriberCollection
     * @param string  $tag                  The tag name of message subscriber services
     * @param string  $keyAttribute         The name of the tag attribute that contains the name of the subscriber
     */
    public function __construct($serviceId, $tag, $keyAttribute)
    {
        $this->serviceId = $serviceId;
        $this->tag = $tag;
        $this->keyAttribute = $keyAttribute;
    }

    /**
     * Search for message subscriber services and provide them as a constructor argument to the message subscriber
     * collection service.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->serviceId)) {
            return;
        }

        $definition = $container->findDefinition($this->serviceId);

        $handlers = array();

        foreach ($container->findTaggedServiceIds($this->tag) as $serviceId => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes[$this->keyAttribute])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The attribute "%s" of tag "%s" of service "%s" is mandatory',
                            $this->keyAttribute,
                            $this->tag,
                            $serviceId
                        )
                    );
                }

                $key = $tagAttributes[$this->keyAttribute];

                $handlers[ltrim($key, '\\')][] = [
                    'id' => $serviceId,
                    'class' => $container->findDefinition($serviceId)->getClass()
                ];
            }
        }

        $definition->replaceArgument(1, $handlers);
    }
}
