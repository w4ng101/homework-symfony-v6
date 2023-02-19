
<?php

namespace App\Services;

trait AverageTemperature {
    private $source;

    public function __construct(Sources $source)
    {
        $this->source = $source;
    }
    public function calcTemperature() {

        $averageTemp = $weatherForecastTemp = 0;
        $cnt = count($this->source->clientsSource());

        if($cnt > 0) {

            foreach ($this->source->clientsSource() as $key => $value) {

                $apiResponse = $this->api->apiCallCurl($value['api_url'],$key);
                if($key == 'weatherapi') {
                    $api_response = ((($apiResponse['temp_f']-32)) * 5/9) + 273.15;
                } else {
                    $api_response = $apiResponse['main']['temp'];
                }
                $averageTemp += $api_response;

            }

            $resultAverageTemp = ($averageTemp / $cnt);
            $weatherForecastTemp = number_format((float)$resultAverageTemp, 2, '.', ''); 
        }
        return $weatherForecastTemp;
    }

}

class ForecastWeather {
    use AverageTemperature;
}
