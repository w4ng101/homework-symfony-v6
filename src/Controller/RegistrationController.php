<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Services\ApiCall;

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

            $apiOpenweathermapResponse = $this->api->apiCallCurl('openweathermap');
            $apiWeatherapiResponse = $this->api->apiCallCurl('weatherapi');
            $averageTemperature = $this->api->averageCalc($apiOpenweathermapResponse['main']['temp'],$apiWeatherapiResponse['temp_f']);

            $user->setTemperature($averageTemperature);
            $em->persist($user);
            $em->flush();
            return $this->json([
                'message' => 'Successfully created',
                'current_weather_forecast' => $averageTemperature            
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
