<?php

namespace Pyrrah\OpenWeatherMapBundle\Services;

use GuzzleHttp\Client as GuzzleClient;
use stdClass;

class Client
{
    /**
     * @var string
     */
    protected $apiUrl = 'https://api.openweathermap.org/data/2.5/';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $units = 'metric';

    /**
     * @var string
     */
    protected $lang = 'en';

    /**
     * @var GuzzleClient
     */
    protected $guzzleClient;

    /**
     * Class constructor.
     *
     * @param string $apiKey
     * @param string $apiUrl
     * @param string $units
     * @param string $lang
     */
    public function __construct($apiKey, $apiUrl = null, $units = null, $lang = null)
    {
        $this->apiKey = $apiKey;

        if ($apiUrl) {
            $this->apiUrl = $apiUrl;
        }

        if ($units) {
            $this->units = $units;
        }

        if ($lang) {
            $this->lang = $lang;
        }

        $this->guzzleClient = new GuzzleClient();
    }

    /**
     * Performs a query to the OpenWeatherMap API.
     *
     * @param $name
     * @param array $parameters
     *
     * @return stdClass
     */
    public function query($name, $parameters = array())
    {
        if (!isset($parameters['APPID'])) {
            $parameters['APPID'] = $this->apiKey;
        }

        if (!isset($parameters['units'])) {
            $parameters['units'] = $this->units;
        }

        if (!isset($parameters['lang'])) {
            $parameters['lang'] = $this->lang;
        }

        $baseUrl = $this->apiUrl.$name;

        $requestQueryParts = array();
        foreach ($parameters as $key => $value) {
            $requestQueryParts[] = $key.'='.rawurlencode($value);
        }
        $baseUrl .= '?'.implode('&', $requestQueryParts);

        $response = $this->guzzleClient->get($baseUrl);
        $response = json_decode($response->getBody()->getContents());

        return $response;
    }

    /**
     * Returns the current weather for a city.
     *
     * @param $city
     * @param array $parameters
     *
     * @return stdClass
     */
    public function getWeather($city, $parameters = array())
    {
        return $this->doGenericQuery('weather', $city, $parameters);
    }

    /**
     * Returns the forecast for a city.
     *
     * @param $city
     * @param $days
     * @param array $parameters
     *
     * @return stdClass
     */
    public function getForecast($city, $days = null, $parameters = array())
    {
        if ($days) {
            if (!empty($parameters)) {
                $parameters['cnt'] = $days;
            } else {
                $parameters = array('cnt' => $days);
            }
        }

        return $this->doGenericQuery('forecast/daily', $city, $parameters);
    }

    /**
     * @param $query
     * @param $city
     * @param array $parameters
     *
     * @return stdClass
     */
    private function doGenericQuery($query, $city, $parameters = array())
    {
        if (is_numeric($city)) {
            $parameters['id'] = $city;
        } else {
            $parameters['q'] = $city;
        }

        $response = $this->query($query, $parameters);

        return $response;
    }
}