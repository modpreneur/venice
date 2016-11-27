<?php

namespace Venice\AppBundle\Entity;

use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BasePaySystem;
use Trinity\Component\EntityCore\Entity\BasePaySystemVendor;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Trinity\NotificationBundle\Annotations as N;

/**
 * Class PaySystemVendor
 */
class PaySystemVendor extends BasePaySystemVendor implements NotificationEntityInterface
{
    /**
     * @var int
     */
    protected $necktieId;

    /**
     * @var bool If the vendor is default for this venice instance.
     * When the venice receives a notification about entity default-billing-plans and the given vendor id is the same as the id of this entity, the default billing plan of given product is changed.
     */
    protected $defaultForVenice;

    /**
     * PaySystemVendor constructor.
     */
    public function __construct()
    {
        $this->defaultForVenice = false;
        $this->name = '';
    }


    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }

    /**
     * @N\AssociationGetter
     *
     * @return BasePaySystem
     */
    public function getPaySystem()
    {
        return $this->paySystem;
    }

    /**
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\PaySystem")
     *
     * @param BasePaySystem $paySystem
     */
    public function setPaySystem($paySystem)
    {
        $this->paySystem = $paySystem;
    }

    /**
     * @return int
     */
    public function getNecktieId()
    {
        return $this->necktieId;
    }

    /**
     * @param int $necktieId
     */
    public function setNecktieId(int $necktieId)
    {
        $this->necktieId = $necktieId;
    }

    /**
     * @return boolean
     */
    public function isDefaultForVenice(): bool
    {
        return $this->defaultForVenice;
    }

    /**
     * @param boolean $defaultForVenice
     */
    public function setDefaultForVenice(bool $defaultForVenice)
    {
        $this->defaultForVenice = $defaultForVenice;
    }


}