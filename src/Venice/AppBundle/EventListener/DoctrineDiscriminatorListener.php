<?php

namespace Venice\AppBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * This Listener listens to the loadClassMetadata event. Upon this event
 * it hooks into Doctrine to update discriminator maps. Adding entries
 * to the discriminator map at parent level is just not nice. We turn this
 * around with this mechanism. In the subclass you will be able to give an
 * entry for the discriminator map. In this listener we will retrieve the
 * load metadata event to update the parent with a good discriminator map,
 * collecting all entries from the subclasses.
 */
class DoctrineDiscriminatorListener implements \Doctrine\Common\EventSubscriber
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * DoctrineDiscriminatorListener constructor.
     * @param array $mapping
     */
    public function __construct($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(\Doctrine\ORM\Events::loadClassMetadata);
    }

    /**
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        $class = $metadata->getReflectionClass();

        if ($class === null) {
            $class = new \ReflectionClass($metadata->getName());
        }

        foreach ($this->mapping as $entityName => $map) {
            if ($class->getName() === $map['entity']) {
                $discriminatorMap = array_merge($metadata->discriminatorMap, $map['map']);
//                $discriminatorMap = array_merge($discriminatorMap, array($entityName => $map['entity']));
                $metadata->setDiscriminatorMap($discriminatorMap);
                break;
            }
        }
    }
}