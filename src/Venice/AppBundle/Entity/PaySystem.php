<?php

namespace Venice\AppBundle\Entity;

use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BasePaySystem;
use Trinity\Component\EntityCore\Entity\BasePaySystemVendor;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Trinity\NotificationBundle\Annotations as N;

/**
 * Class PaySystem
 */
class PaySystem extends BasePaySystem implements NotificationEntityInterface
{
    /**
     * @var int
     */
    protected $necktieId;

    /**
     * PaySystem constructor.
     */
    public function __construct()
    {
        $this->name = '';
    }

    /**
     * @N\AssociationGetter
     *
     * @return BasePaySystemVendor
     */
    public function getDefaultVendor()
    {
        return $this->defaultVendor;
    }

    /**
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\PaySystemVendor")
     *
     * @param BasePaySystemVendor $defaultVendor
     */
    public function setDefaultVendor($defaultVendor)
    {
        $this->defaultVendor = $defaultVendor;
    }


    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
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
}