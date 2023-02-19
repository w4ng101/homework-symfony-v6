<?php
namespace App\Services;
use App\Services\Sources;
use App\Services\ApiCall;

trait AverageTemperature {

    private $source;
    private $api;

    public function __construct(Sources $source,ApiCall $api)
    {
        $this->source = $source;
        $this->api = $api;
    }
    public function calcTemperature($params) {

        $averageTemp = $weatherForecastTemp = 0;
        $apiEndPointSource = $this->source->clientsSource($params);
        $cnt = count($apiEndPointSource);

        if($cnt > 0) {

            foreach ($apiEndPointSource as $key => $value) {

                $apiResponse = $this->api->apiCallCurl($value['api_url'],$key);
                if($key == 'weatherapi') {
                    $api_response = !empty($apiResponse['temp_f']) ? ((($apiResponse['temp_f']-32)) * 5/9) + 273.15 : $weatherForecastTemp;
                } else {
                    $api_response = !empty($apiResponse['main']) ? $apiResponse['main']['temp'] : $weatherForecastTemp;
                }
                $averageTemp += $api_response;

            }

            $resultAverageTemp = ($averageTemp / $cnt);
            $weatherForecastTemp = number_format((float)$resultAverageTemp, 2, '.', ''); 
        }
        return $weatherForecastTemp;
    }

}

class WeatherForecast {
    use AverageTemperature;
}
