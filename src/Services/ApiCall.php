<?php
namespace App\Services;
use App\Services\Sources;
use App\Services\Config;

trait ApiCurl {
    private $apiKey;

    public function __construct(Config $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    public function apiCallCurl($apiEndPoint,$apiName) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $apiEndPoint);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);

        // Note : Weather forecast result totally different in every api source response.
        $weather_result = json_decode($response, true);

        if (empty($weather_result)) {
            return [];
        }

        $apiArrKeyIndexName = array_keys($this->apiKey->apiKey());
        // Note: $apiArrKeyIndexName[0] = openweathermap
        // $apiArrKeyIndexName[1] = weatherapi
        // Can able to add multiple make it sure same index in Services\Config file.
        if ($apiName == $apiArrKeyIndexName[0]) {
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
    use ApiCurl;
}
