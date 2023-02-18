<?php
namespace App\Services;

trait AverageTemperature {

    public function averageCalc($openweathermap,$weatherapi) {

        $average_temperature = 0;
        if (!empty($openweathermap) && !empty($weatherapi)) {
            $average_temperature = ((((int)$openweathermap) + ((int) (((($weatherapi-32)) * 5/9) + 273.15)) ) / 2);
        } else {
            if (!empty($openweathermap)) {
                $average_temperature = $openweathermap;
            }
            if (!empty($weatherapi)) {
                $average_temperature = $weatherapi;
            }
        }
        return $average_temperature;
    }

}

trait ApiCurl {

    public function apiCallCurl($sources) {

        $return_apiKey_value = match ($sources) {
            'openweathermap' => "http://api.openweathermap.org/data/2.5/weather?q=Cebu%20City,ph&appid=d7fcc9556025fb0dfca2db1149a64549",
            'weatherapi' => "https://api.weatherapi.com/v1/current.json?key=5ea344b89fd24211886154726231802&q=Cebu%20City",
        };

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $return_apiKey_value);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);

        // Note : Weather result totally different in every api source response.
        $weather_result = json_decode($response, true);

        if (empty($weather_result)) {
            return [];
        }

        if ($sources == 'openweathermap') {
            return $weather_result;
        }

        $result = [];
        foreach ($weather_result as $key => $value) {
            $result = $value;
        }
        return $result;
    }

}

class ApiCall {
    use AverageTemperature, ApiCurl;
}
