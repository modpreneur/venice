<?php
namespace Venice\AppBundle\Entity\Interfaces;


/**
 * Class OAuthToken.
 */
interface OAuthTokenInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @param string $accessToken
     *
     * @return OAuthTokenInterface
     */
    public function setAccessToken($accessToken);

    /**
     * @return string
     */
    public function getRefreshToken();

    /**
     * @param string $refreshToken
     *
     * @return OAuthTokenInterface
     */
    public function setRefreshToken($refreshToken);

    /**
     * @return \DateTime
     */
    public function getValidTo();

    /**
     * @return bool
     */
    public function isAccessTokenValid();

    /**
     * @param \DateTime $validTo
     *
     * @return OAuthTokenInterface
     */
    public function setValidTo(\DateTime $validTo);

    /**
     * Set validTo by current time + given lifetime.
     *
     * @param $lifetime
     *
     * @return OAuthTokenInterface
     */
    public function setValidToByLifetime($lifetime);

    /**
     * @return string
     */
    public function getScope();

    /**
     * @param string $scope
     *
     * @return OAuthTokenInterface
     */
    public function setScope($scope);

    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @param mixed $user
     *
     * @return OAuthTokenInterface
     */
    public function setUser($user);

    /**
     * Returns createdAt value.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps();
}