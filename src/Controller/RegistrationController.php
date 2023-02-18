<?php

namespace App\Controller;

use App\Services\ApiCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    private $api;

    public function __construct(ApiCall $api)
    {
        $this->api = $api;
    }

    #[Route('/registration', name: 'app_registration',methods: ['POST'])]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            $em = $doctrine->getManager();
            $decoded = json_decode($request->getContent());
            $email = $decoded->email;
            $name = $decoded->name;
            $city = $decoded->city;
            $country = $decoded->country;
            $plaintextPassword = $decoded->password;

            // Query your non-existent table
            $user = new User();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setName($name);
            $user->setCity($city);
            $user->setCountry($country);

            $api_openweathermap_response = $this->api->apiCallCurl('openweathermap');
            $api_weatherapi_response = $this->api->apiCallCurl('weatherapi');
            $average_temperature = $this->api->averageCalc($api_openweathermap_response['main']['temp'],$api_weatherapi_response['temp_f']);

            $user->setTemperature($average_temperature);
            $em->persist($user);
            $em->flush();
            return $this->json([
                'message' => 'Successfully created',
                'current_weather_forecast' => $average_temperature            
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function apiCall($sources) {
        $return_apiKey_value = match ($sources) {
            'openweathermap' => "http://api.openweathermap.org/data/2.5/weather?q=Cebu%20City,ph&appid=d7fcc9556025fb0dfca2db1149a64549",
            'weatherapi' => "https://api.weatherapi.com/v1/current.json?key=5ea344b89fd24211886154726231802&q=Cebu%20City",
        };
        $googleApiUrl = $return_apiKey_value;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        $weather = json_decode($response, true);
        if ($sources == 'openweathermap') {
            return $weather;
        }
        $result = [];
        foreach ($weather as $key => $value) {
            $result = $value;
        }
        return $result;
    }
}
