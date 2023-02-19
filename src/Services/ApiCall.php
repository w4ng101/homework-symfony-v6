<?php
namespace App\Services;

trait ApiCurl {

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

        if ($apiName == 'openweathermap' || $apiName == 'openweathermapv2') {
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
