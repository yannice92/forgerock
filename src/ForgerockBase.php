<?php
/**
 * Created by PhpStorm.
 * User: SALT
 * Date: 14/10/2019
 * Time: 10:32
 */

namespace App\Forgerock;

use GuzzleHttp\Client;


class ForgerockBase
{
    protected $client;

    function __construct()
    {
        $guzzleClientOptions = [
            // Base URI is used with relative requests
            'base_uri' => env('FR_DOMAIN'),
            // You can set any number of default request options.
            'timeout' => 15.0,
            'verify' => false,
        ];
        $this->client = new Client($guzzleClientOptions);
    }

    protected function getRequest($uri, $queryString = [], $headers = ['Accept' => 'application/json'])
    {
        $response = $this->client->get($uri, [
                'query' => $queryString,
                'headers' => $headers,
            ]
        );
        return \GuzzleHttp\json_decode($response->getBody()->getContents());
    }

    protected function postRequest($uri, $queryString, $body = [], $headers = ['Accept' => 'application/json'])
    {
        $response = $this->client->post($uri, [
            'json' => $body,
            'query' => $queryString,
            'headers' => $headers
        ]);
        return \GuzzleHttp\json_decode($response->getBody()->getContents());
    }

    protected function deleteRequest($uri)
    {
        $response = $this->client->delete($uri);
        return \GuzzleHttp\json_decode($response->getBody()->getContents());
    }

    protected function request($method, $uri, $queryString, $body = [], $headers = ['Accept' => 'application/json'])
    {
        $response = $this->client->request($method, $uri, [
            'json' => $body,
            'headers' => $headers,
            'query' => $queryString,
            'http_errors' => false
        ]);

        return $response;
    }
}
