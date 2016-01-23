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

    /** @var  bool|null The value which will be returned by all methods if not null. */
    protected $forceReturn;


    /**
     * AppLogic constructor.
     * @param ContainerInterface $container
     * @param $forceReturn bool|null If bool all methods will return the given value
     */
    public function __construct(ContainerInterface $container, $forceReturn = null)
    {
        $this->container = $container;
        $this->connectedToNecktie = $this->container->hasParameter("necktie_url");

        $this->setForceReturn($forceReturn);
    }

    /**
     * Set the value which will be returned by all methods.
     *
     * @param $forceReturn bool|null Bool to force return given value. Null to return the default logic value.
     */
    public function setForceReturn($forceReturn)
    {
        if (($forceReturn !== null) && (!is_bool($forceReturn))) {
            throw new \InvalidArgumentException("The forceReturn has to be null or bool!");
        }

        $this->forceReturn = $forceReturn;
    }

    /**
     * Is the logic in the test mode?
     *
     * @return bool
     */
    public function hasForceReturn()
    {
        return is_bool($this->forceReturn);
    }

    /**
     * Is the app connected to necktie.
     *
     * @return bool
     */
    public function connectedToNecktie():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return $this->connectedToNecktie;
    }


    /**
     * Allow adding new ProductAccesses.
     *
     * @return bool
     */
    public function allowAddingNewProductAccesses():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Display necktie id field in ProductAccess template.
     *
     * @return bool
     */
    public function displayNecktieFieldForProductAccess():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return $this->connectedToNecktie;
    }


    /**
     * Display edit tab in ProductAccess template.
     *
     * @return bool
     */
    public function displayEditTabForProductAccess():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Display delete tab in ProductAccess template.
     *
     * @return bool
     */
    public function displayDeleteTabForProductAccess():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Allow adding new billing plans.
     *
     * @return bool
     */
    public function allowAddingNewBillingPlans():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Display amember id field in ProductAccess template
     *
     * @return bool
     */
    public function displayAmemberFieldForBillingPlan():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Display necktie id field in ProductAccess template
     *
     * @return bool
     */
    public function displayNecktieFieldForBillingPlan():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return $this->connectedToNecktie;
    }


    /**
     * Display edit tab in ProductAccess template.
     *
     * @return bool
     */
    public function displayEditTabForBillingPlan():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }


    /**
     * Display edit tab in ProductAccess template.
     *
     * @return bool
     */
    public function displayDeleteTabForBillingPlan():bool
    {
        if ($this->hasForceReturn()) {
            return $this->forceReturn;
        }

        return !$this->connectedToNecktie;
    }
}