<?php
namespace App\Services;

trait ClientKeys {

    public function apiKey() {
        return [
            'openweathermap' => 'd7fcc9556025fb0dfca2db1149a64549',
            'weatherapi' => '5ea344b89fd24211886154726231802',            
            'openweathermapv2' => 'd7fcc9556025fb0dfca2db1149a64549',
        ];
    }

}

class Config {
    use ClientKeys;
}
