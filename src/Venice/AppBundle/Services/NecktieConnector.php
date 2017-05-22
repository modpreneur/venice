<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 13:02.
 */
namespace Venice\AppBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\MessageInterface;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;

class NecktieConnector
{
    /** @var  ClientInterface */
    protected $client;

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set client object.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a client with $baseUri and set ti to the $client property.
     *
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri)
    {
        $client = new Client(['base_uri' => $baseUri]);

        $this->setClient($client);
    }

    /**
     * @param User $user
     * @param $method string
     * @param $uri string
     * @param array $data
     * @param null|string $accessToken
     * @param bool $sendAsJson
     *
     * @return null|string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    public function getResponse(
        User $user = null,
        string $method,
        string $uri,
        array $data = [],
        string $accessToken = null,
        bool $sendAsJson = false
    ) {
        $accessToken = $this->getAccessToken($user, $accessToken);
        $options = $this->prepareOptions($method, $data, $accessToken, $sendAsJson);

        $response = null;

        try {
            $response = $this->createRequest($method, $uri, $options);
        } catch (ServerException $e) {
            throw new UnsuccessfulNecktieResponseException($e->getResponse()->getBody()->getContents());
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                return;
            }

            throw new UnsuccessfulNecktieResponseException($e->getResponse()->getBody()->getContents());
        }

        return $this->getBodyFromResponse($response);
    }

    /**
     * Create request and return response.
     *
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function createRequest(string $method, string $uri, array $options = [])
    {
        return $this->client->request(
            $method,
            $uri,
            $options
        );
    }

    /**
     * Get User's access token or provided access token.
     *
     * @param User $user
     * @param string $accessTokenString
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getAccessToken(User $user = null, string $accessTokenString = null)
    {
        if ($user !== null) {
            $usersAccessToken = $user->getLastAccessToken();

            if ($usersAccessToken !== null) {
                return $usersAccessToken;
            }
        }

        if ($accessTokenString === null) {
            throw new \Exception('User has no access token or the given accessToken is null.');
        }

        return $accessTokenString;
    }

    /**
     * Prepare options array.
     *
     * @param $method
     * @param $data
     * @param $accessToken
     * @param $sendAsJson
     *
     * @return array
     */
    protected function prepareOptions(
        string $method,
        array $data = [],
        string $accessToken = null,
        bool $sendAsJson = false
    ) {
        $options = ['headers' => ['Authorization' => "Bearer {$accessToken}"]];

        if (strtolower($method) === 'get') {
            $options['query'] = $data;
        } elseif (strtolower($method) === 'post') {
            if ($sendAsJson) {
                $options['json'] = $data;
            } else {
                $options['form_params'] = $data;
            }
        }

        return $options;
    }

    /**
     * @param MessageInterface $response
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getBodyFromResponse(MessageInterface $response)
    {
        return $response->getBody()->getContents();
    }
}
