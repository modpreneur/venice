<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 13:14.
 */
namespace Venice\AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * {@inheritdoc}
 */
class VeniceAppExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     *
     * @throws \Exception
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $container->setParameter('venice.app.entity_mapping', $config['entity_mapping']);
        $container->setParameter('venice.app.forms_override', $config['forms_override']);
        $container->setParameter('venice.app.entity_forms', $config['entity_forms']);
        $container->setParameter('venice.app.entity_override', $config['entity_override']);
    }
}
