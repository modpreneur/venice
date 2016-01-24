<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 13:02
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class NecktieConnector
{
    /** @var  ClientInterface */
    protected $client;


    public function setBaseUri(string $baseUri)
    {
        $this->client = new Client(["base_uri" => $baseUri]);
    }


    /**
     * @param User $user
     * @param $method string
     * @param $uri string
     * @param array $parameters
     * @param null|string $accessToken
     * @param bool $sendAsJson
     * @param bool $jsonDecodeResponse
     *
     * @return null|string
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    public function getResponse(
        User $user = null,
        string $method,
        string $uri,
        array $parameters = [],
        string $accessToken = null,
        bool $sendAsJson = false,
        bool $jsonDecodeResponse = true
    )
    {
        $accessToken = $this->getAccessToken($user, $accessToken);
        $options = $this->prepareOptions($method, $parameters, $accessToken, $sendAsJson);

        $response = null;
        try {
            $response = $this->client->request(
                $method,
                $uri,
                $options
            );
        } catch (ServerException $e) {
            throw new UnsuccessfulNecktieResponseException($e->getResponse()->getBody()->getContents());
        }
        catch (ClientException $e) {
            if($e->getCode() === 404) {
                return null;
            }
        }

        if($jsonDecodeResponse) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return $response->getBody()->getContents();
    }

    /**
     * Get User's access token or provided access token
     *
     * @param $user
     * @param $accessToken
     * @return mixed
     * @throws \Exception
     */
    protected function getAccessToken(User $user = null, string $accessToken = null)
    {
        if ($user) {
            $accessToken = $user->getLastAccessToken();
        } else if (!$accessToken) {
            throw new \Exception("User has no access token or the given accessToken is null.");
        }

        return $accessToken;
    }

    /**
     * Prepare options array.
     *
     * @param $method
     * @param $parameters
     * @param $accessToken
     * @param $sendAsJson
     * @return array
     */
    protected function prepareOptions($method, $parameters, $accessToken, $sendAsJson)
    {
        $options = ["headers" => ["Authorization" => "Bearer {$accessToken}"],];

        if (strtolower($method) === "get") {
            $options["query"] = $parameters;
        } else if (strtolower($method) === "post") {
            if ($sendAsJson) {
                $options["json"] = $parameters;
            } else {
                $options["form_params"] = $parameters;
            }
        }

        return $options;
    }
}