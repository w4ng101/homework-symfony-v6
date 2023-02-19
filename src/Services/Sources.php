<?php
namespace App\Services;
use App\Services\Config;

trait Clients {
    private $apiKey;

    public function __construct(Config $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function clientsSource() {
        return [
            'openweathermap' => [
                'api_url' => 'http://api.openweathermap.org/data/2.5/weather?q=Cebu%20City,ph&appid='.$this->apiKey->apiKey()['openweathermap'],
            ],
            'weatherapi' => [
                'api_url' => 'https://api.weatherapi.com/v1/current.json?key='.$this->apiKey->apiKey()['weatherapi'].'&q=Cebu%20City',
            ],
            'openweathermapv2' => [
                'api_url' => 'http://api.openweathermap.org/data/2.5/weather?q=Butuan%20City,ph&appid='.$this->apiKey->apiKey()['openweathermapv2'],
            ],
        ];
    }

}

class Sources {
    use Clients;
}
