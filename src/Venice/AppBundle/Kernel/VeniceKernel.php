<?php

namespace Venice\AppBundle\Kernel;

use Symfony\Component\HttpKernel\Kernel;

/**
 * {@inheritdoc}
 */
abstract class VeniceKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \EmanueleMinotto\TwigCacheBundle\TwigCacheBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \KMS\FroalaEditorBundle\KMSFroalaEditorBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Snc\RedisBundle\SncRedisBundle(),

            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new \Venice\AppBundle\VeniceAppBundle(),
            new \Venice\AdminBundle\VeniceAdminBundle(),
            new \Venice\FrontBundle\VeniceFrontBundle(),
            new \Venice\BunnyBundle\VeniceBunnyBundle(),

            new \WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),

            // Trinity
            new \Trinity\FrameworkBundle\TrinityFrameworkBundle(),
            new \Trinity\NotificationBundle\TrinityNotificationBundle(),
            new \Trinity\Bundle\MessagesBundle\TrinityMessagesBundle(),
            new \Trinity\AdminBundle\TrinityAdminBundle(),
            new \Trinity\Bundle\SettingsBundle\SettingsBundle(),
            new \Trinity\Bundle\SearchBundle\SearchBundle(),
            new \Trinity\Bundle\GridBundle\GridBundle(),
            new \Trinity\Bundle\BunnyBundle\TrinityBunnyBundle(),
            new \Trinity\Bundle\LoggerBundle\LoggerBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
        }

        return $bundles;
    }
}
