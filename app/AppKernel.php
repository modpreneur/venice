<?php

use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends \AppBundle\Kernel\VeniceKernel
{
    public function getRootDir()
    {
        return __DIR__;
    }


    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }


    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }


    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
