<?php
namespace Venice\AppBundle\Entity\Interfaces;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\Core\Interfaces\UserInterface as TrinityUserInterface;


/**
 * Class User.
 */
interface UserInterface extends TrinityUserInterface, BaseEntityInterface
{
    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return $this
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * Set website.
     *
     * @param string $website
     *
     * @return $this
     */
    public function setWebsite($website);

    /**
     * Set avatar.
     *
     * @param string $avatar
     *
     * @return $this
     */
    public function setAvatar($avatar);

    /**
     * Get public.
     *
     * @return bool
     */
    public function isPublic();

    /**
     * Set public.
     *
     * @param bool $public
     *
     * @return UserInterface
     */
    public function setPublic($public);

    /**
     * Is Admin.
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Is SuperAdmin.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * Set Admin.
     *
     * @param bool $admin
     *
     * @return UserInterface
     */
    public function setAdmin($admin);

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

    /**
     * {@inheritdoc}
     */
    public function getPassword();

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken();

    /**
     * {@inheritdoc}
     */
    public function getRoles();

    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword();

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired();

    /**
     * @param DateTime $birthDate
     *
     * @return UserInterface
     */
    public function setBirthDate(DateTime $birthDate);

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials();

    /**
     * {@inheritdoc}
     */
    public function removeRole($role);

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical);

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt);

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null);

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles);

    /**
     * {@inheritdoc}
     */
    public function getGroups();

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name);

    /**
     * @return string
     */
    public function getPreferredUnits();

    /**
     * @param string $preferredUnits
     *
     * @return UserInterface
     *
     * @throws \InvalidArgumentException
     */
    public function setPreferredUnits($preferredUnits);

    /**
     * Get the last refresh token string.
     *
     * @return null|string
     */
    public function getLastAccessToken();

    /**
     * Get all OAuthTokens.
     *
     * @return ArrayCollection<OAuthToken>
     */
    public function getOAuthTokens();

    /**
     * Get the full name of the user if set. Return username otherwise.
     *
     * @return string
     */
    public function getFullNameOrUsername();

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasAccessToProduct(ProductInterface $product);

    /**
     * {@inheritdoc}
     */
    public function addRole($role);

    /**
     * {@inheritdoc}
     */
    public function serialize();

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized);

    /**
     * {@inheritdoc}
     */
    public function getUsername();

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical();

    /**
     * {@inheritdoc}
     */
    public function getSalt();

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical();

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * {@inheritdoc}
     */
    public function hasRole($role);

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired();

    /**
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function addProductAccess(ProductAccessInterface $productAccess);

    /**
     * @return DateTime
     */
    public function getBirthDate();

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked();

    public function isEnabled();

    /**
     * {@inheritdoc}
     */
    public function setUsername($username);

    /**
     * {@inheritdoc}
     */
    public function setEmail($email);

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical);

    /**
     * {@inheritdoc}
     */
    public function setPassword($password);

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password);

    /**
     * {@inheritdoc}
     */
    public function getGroupNames();

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group);

    /**
     * @return ArrayCollection<ProductAccess>
     */
    public function getProductAccesses();

    /**
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function removeProductAccess(ProductAccessInterface $productAccess);

    /**
     * @param int $necktieId
     *
     * @return UserInterface
     */
    public function setNecktieId($necktieId);

    /**
     * Get the last OAuthToken object.
     *
     * @return OAuthTokenInterface|null
     */
    public function getLastToken();

    /**
     * @param OAuthTokenInterface $OAuthToken
     *
     * @return $this
     */
    public function removeOAuthToken(OAuthTokenInterface $OAuthToken);

    /**
     * @param boolean $locked
     */
    public function setLocked(bool $locked);

    /**
     * @param ProductInterface $product
     * @param DateTime $fromDate
     * @param DateTime|null $toDate
     * @param int|null $necktieId
     *
     * @return ProductAccessInterface|null
     */
    public function giveAccessToProduct(ProductInterface $product, \DateTime $fromDate, \DateTime $toDate = null, $necktieId = null);

    /**
     * {@inheritdoc}
     */
    public function getEmail();

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean);

    /** @return ClientInterface[] */
    public function getClients();

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean);

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt();

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group);

    /**
     * Check if the last OAuth access token is valid based on stored lifetime.
     *
     * @return bool
     */
    public function isLastAccessTokenValid();

    /**
     * Get the last refresh token string.
     *
     * @return string
     */
    public function getLastRefreshToken();

    /**
     * @param OAuthTokenInterface $OAuthToken
     *
     * @return $this
     */
    public function addOAuthToken(OAuthTokenInterface $OAuthToken);

    /**
     * @return boolean
     */
    public function isLocked() : bool;

    /**
     * Get productAccess entity for this user and given product.
     *
     * @param ProductInterface $product
     *
     * @return ProductAccessInterface|null
     */
    public function getProductAccess(ProductInterface $product);
}