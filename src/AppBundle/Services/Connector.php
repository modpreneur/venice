<?php
/**
 * Created by PhpStorm.
 * User: ondrejbohac
 * Date: 10.07.15
 * Time: 12:28
 */

namespace AppBundle\Services;


use Anchovy\CURLBundle\CURL\Curl;

class Connector
{
    /** @var  Curl */
    protected $curl;

    function __construct($curl)
    {
        $this->curl = $curl;
    }

    public function getJson($url)
    {
        $response = $this->curl->setMethod("GET")->setURL($url)->execute();

        return json_decode($response, true);
    }

    public function putAndGetJson($url,$parameters = array())
    {
        $response = $this->curl->setMethod("PUT")->setURL($url)->execute();

        return json_decode($response, true);
    }

    public function postAndGetJson($url,$parameters = array(), $options = array())
    {
        foreach($options as $key => $option)
        {
            $this->curl->setOption($key, $option);
        }

        $response = $this->curl->setMethod("POST",$parameters)->setURL($url)->execute();

        return json_decode($response, true);
    }

    public function postJson($url, $parameters = array())
    {
        $jsonData = json_encode($parameters);

        $request = $this->curl->setMethod("POST",array())->setURL($url);
        $request->setOption("CURLOPT_POSTFIELDS", $jsonData);
        $request->setOption("CURLOPT_RETURNTRANSFER", true);
        $request->setOption("CURLOPT_HTTPHEADER", array(
                'Content-Type: application/json',
                'Content-Length: '.strlen($jsonData))
        );
        $response = $request->execute();

        return json_decode($response, true);
    }

    public function get($url, $accessToken, $parameters = [], $options = [], $headers = [])
    {
        $headers[] = "Authorization: Bearer " . $accessToken;

        $request = $this->curl->setURL($url);
        $request->setMethod("GET", $parameters);
        $request->setOptions($options);
        $request->setOption("CURLOPT_HTTPHEADER", $headers);

        $response = $request->execute();

        return $response;
    }

}