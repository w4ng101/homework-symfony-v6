<?php
namespace App\Services;

trait Resources {
    public function apiKey() {
        // Can able to add multiple sources into array. Make it sure you already declare the api key in .env files
        // Note: index 0 = openweathermap
        // index 1 = weatherapi
        return [
            'openweathermap' => $_ENV["OPENWEATHERMAP_API_KEY"],
            'weatherapi' => $_ENV["WEATHERAPI_API_KEY"],            
        ];
    }

}

class Config {
    use Resources;
}
