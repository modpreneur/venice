<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 9:27.
 */
namespace Venice\AppBundle\Entity;

use Venice\AppBundle\Entity\Interfaces\OAuthTokenInterface;
use Venice\AppBundle\Traits\Timestampable;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class OAuthToken.
 */
class OAuthToken implements OAuthTokenInterface
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     * @Serializer\Exclude()
     */
    protected $user;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * Datetime to which will be token valid.
     *
     * @var \DateTime
     */
    protected $validTo;

    /**
     * @var string
     */
    protected $scope;

    public function __construct()
    {
        $this->updateTimestamps();
    }

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
     * @return OAuthTokenInterface
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
     * @return OAuthTokenInterface
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
     * @return OAuthTokenInterface
     */
    public function setValidTo(\DateTime $validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Set validTo by current time + given lifetime.
     *
     * @param $lifetime
     *
     * @return OAuthTokenInterface
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
     * @return OAuthTokenInterface
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
     * @return OAuthTokenInterface
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
