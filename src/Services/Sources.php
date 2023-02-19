<?php
namespace App\Services;
use App\Services\Config;

trait Clients {
    private $apiKey;

    public function __construct(Config $apiKey)
    {
        $this->apiKey = $apiKey;
    }
        // Can able to add multiple sources into array. Please make it sure same setup on Services\Config file.
    public function clientsSource($params) {
        return [
            'openweathermap' => [
                'api_url' => 'http://api.openweathermap.org/data/2.5/weather?q='.urlencode($params['city']).','.urlencode($params['country']).'&appid='.$this->apiKey->apiKey()['openweathermap'],
            ],
            'weatherapi' => [
                'api_url' => 'https://api.weatherapi.com/v1/current.json?key='.$this->apiKey->apiKey()['weatherapi'].'&q='.urlencode($params['city']),
            ],
        ];
    }

}

class Sources {
    use Clients;
}
