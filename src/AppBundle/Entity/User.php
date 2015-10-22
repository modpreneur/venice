<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36
 */

namespace AppBundle\Entity;

use \DateTime;
use \InvalidArgumentException;
use APY\DataGridBundle\Grid\Mapping\Column;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseUser as TrinityUser;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * @ORM\Table(name="user")
 *
 * @package AppBundle\Entity
 */
class User extends TrinityUser
{
    const PREFERRED_UNITS_IMPERIAL = "imperial";
    const PREFERRED_UNITS_METRIC = "metric";

    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_UNITS_IMPERIAL;

    /**
     * ORM\@Column(name="necktie_id", type="integer", unique=true, nullable=true)
     *
     * @var integer
     */
    protected $necktieId;

    /**
     * ORM\@Column(name="amember_id", type="integer", unique=true, nullable=true)
     *
     * @var integer
     */
    protected $amemberId;

    /**
     * @ORM\Column(name="preferred_units", type="string", length=10)
     *
     * @var string
     */
    protected $preferredUnits;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="user", cascade={"REMOVE"})
     *
     * @var ArrayCollection<ProductAccess>
     */
    protected $productAccesses;

    /**
     * @ORM\Column(name="date_of_birth", type="date")
     *
     * @var DateTime
     */
    protected $birthDate;


    /**
     * @var ArrayCollection<NecktieToken>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\NecktieToken", mappedBy="user", cascade={"remove", "persist"})
     */
    protected $necktieTokens;


    public function __construct()
    {
        parent::__construct();

        $this->password = "";
        $this->salt = "";
        $this->productAccesses = new ArrayCollection();
        $this->necktieTokens = new ArrayCollection();
        $this->preferredUnits = self::DEFAULT_PREFERRED_METRICS;
        $this->birthDate = new DateTime();
    }


    /**
     * @return int
     */
    public function getNecktieId()
    {
        return $this->necktieId;
    }


    /**
     * @return string
     */
    public function getPreferredUnits()
    {
        return $this->preferredUnits;
    }


    /**
     * @param string $preferredUnits
     *
     * @return User
     */
    public function setPreferredUnits($preferredUnits)
    {
        if($preferredUnits !== self::PREFERRED_UNITS_METRIC || $preferredUnits !== self::PREFERRED_UNITS_IMPERIAL)
        {
            throw new InvalidArgumentException(
                "Preferred units has to be one of " .
                self::PREFERRED_UNITS_METRIC .
                " or " .
                self::PREFERRED_UNITS_IMPERIAL .
                ", $preferredUnits given.");
        }

        $this->preferredUnits = $preferredUnits;

        return $this;
    }

    /**
     * @return ArrayCollection<ProductAccess>
     */
    public function getProductAccesses()
    {
        return $this->productAccesses;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function addProductAccess(ProductAccess $productAccess)
    {
        if(!$this->productAccesses->contains($productAccess))
        {
            $this->productAccesses->add($productAccess);
        }

        return $this;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function removeProductAccess(ProductAccess $productAccess)
    {
        $this->productAccesses->remove($productAccess);

        return $this;
    }



    /**
     * @return DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }


    /**
     * @param DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate(DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }


    /**
     * @param int $amemberId
     *
     * @return User
     */
    public function setAmemberId($amemberId)
    {
        $this->amemberId = $amemberId;

        return $this;
    }


    /**
     * @param int $necktieId
     *
     * @return User
     */
    public function setNecktieId($necktieId)
    {
        $this->necktieId = $necktieId;

        return $this;
    }


    /**
     * @return string
     */
    public function getLastAccessToken()
    {
        return $this->necktieTokens->last()->getAccessToken();
    }


    /**
     * @return string
     */
    public function getLastRefreshToken()
    {
        return $this->necktieTokens->last()->getRefreshToken();
    }


    /**
     * @return ArrayCollection<NecktieToken>
     */
    public function getNecktieTokens()
    {
        return $this->necktieTokens;
    }


    /**
     * @param NecktieToken $necktieToken
     * @return $this
     */
    public function addNecktieToken(NecktieToken $necktieToken)
    {
        if(!$this->necktieTokens->contains($necktieToken))
        {
            $this->necktieTokens->add($necktieToken);
        }

        return $this;
    }


    /**
     * @param NecktieToken $necktieToken
     * @return $this
     */
    public function removeNecktieToken(NecktieToken $necktieToken)
    {
        $this->necktieTokens->remove($necktieToken);

        return $this;
    }




}