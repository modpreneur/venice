<?php

namespace AppBundle\Kernel;

use Symfony\Component\HttpKernel\Kernel;

abstract class VeniceKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \KMS\FroalaEditorBundle\KMSFroalaEditorBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Snc\RedisBundle\SncRedisBundle(),

            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new \AppBundle\AppBundle(),
            new \AdminBundle\AdminBundle(),
            new \FrontBundle\FrontBundle(),

            new \WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            new \Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),

            // Trinity
            new \Trinity\FrameworkBundle\TrinityFrameworkBundle(),
            new \Trinity\NotificationBundle\TrinityNotificationBundle(),
            new \Trinity\AdminBundle\TrinityAdminBundle(),
            new \Trinity\Bundle\SettingsBundle\SettingsBundle(),
            new \Trinity\Bundle\SearchBundle\SearchBundle(),
            new \Trinity\Bundle\GridBundle\GridBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }
}