<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.01.16
 * Time: 15:45
 */

namespace AppBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;

class AppLogic
{
    /** @var  ContainerInterface */
    protected $container;

    /** @var  bool */
    protected $connectedToNecktie;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->connectedToNecktie = $this->container->has("necktie_url");
    }


    /**
     * Is the app connected to necktie.
     *
     * @return bool
     */
    public function connectedToNecktie():bool
    {
        return $this->connectedToNecktie;
    }


    /**
     * Allow adding new ProductAccesses.
     *
     * @return bool
     */
    public function allowAddingNewProductAccesses():bool
    {
        return !$this->connectedToNecktie;
    }


    /**
     * Display necktie field in product access template
     *
     * @return bool
     */
    public function displayNecktieFieldForProductAccess():bool
    {
        return $this->connectedToNecktie;
    }


    /**
     * Display edit tab in ProductAccess template
     *
     * @return bool
     */
    public function displayEditTabForProductAccess():bool
    {
        return !$this->connectedToNecktie;
    }

    /**
     * Display delete tab in ProductAccess template
     *
     * @return bool
     */
    public function displayDeleteTabForProductAccess():bool
    {
        return !$this->connectedToNecktie;
    }
}