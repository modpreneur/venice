<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 9:27
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="oauth_token")
 *
 * Class OAuthToken
 * @package AppBundle\Entity
 */
class OAuthToken
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="necktieTokens")
     */
    protected $user;


    /**
     * @var string
     *
     * @ORM\Column(type="access_token", type="string")
     */
    protected $accessToken;


    /**
     * @var string
     *
     * @ORM\Column(type="refresh_token", type="string")
     */
    protected $refreshToken;


    /**
     * Datetime to which will be token valid
     *
     * @var \DateTime
     *
     * @ORM\Column(type="access_token", type="datetime")
     */
    protected $validTo;


    /**
     * @var string
     *
     * @ORM\Column(type="scope", type="string", nullable=true)
     */
    protected $scope;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    /**
     * @param string $accessToken
     *
     * @return OAuthToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }


    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }


    /**
     * @param string $refreshToken
     *
     * @return OAuthToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getValidTo()
    {
        return $this->validTo;
    }


    /**
     * @return bool
     */
    public function isAccessTokenValid()
    {
         return $this->validTo->getTimestamp() > (new \DateTime())->getTimestamp();
    }


    /**
     * @param \DateTime $validTo
     *
     * @return OAuthToken
     */
    public function setValidTo(\DateTime $validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }


    /**
     * Set validTo by current time + given lifetime
     *
     * @param $lifetime
     *
     * @return OAuthToken
     *
     */
    public function setValidToByLifetime($lifetime)
    {
        $this->validTo = new \DateTime();
        $this->validTo->setTimestamp(
            $this->validTo->getTimestamp() + $lifetime
        );

        return $this;
    }


    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }


    /**
     * @param string $scope
     *
     * @return OAuthToken
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param mixed $user
     *
     * @return OAuthToken
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


}