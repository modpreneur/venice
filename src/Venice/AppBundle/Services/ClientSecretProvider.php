<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.05.16
 * Time: 19:01
 */

namespace Venice\AppBundle\Services;

use Trinity\Bundle\MessagesBundle\Interfaces\SecretKeyProviderInterface;

/**
 * Class ClientSecretProvider
 * @package Venice\AppBundle\Services
 */
class ClientSecretProvider implements SecretKeyProviderInterface
{
    /** @var string */
    protected $clientSecret;

    
    /**
     * ClientSecretProvider constructor.
     * @param string $clientSecret
     */
    public function __construct(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }


    /**
     * @param string $clientId
     *
     * @return string Client secret
     */
    public function getSecretKey(string $clientId)
    {
        return $this->clientSecret;
    }
}
