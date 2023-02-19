<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Services\WeatherForecast;
use App\Common\Countries;
use App\Common\ErrorResponse;


#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    private $forecastWeather;
    private $country;
    private $errMsg;

    public function __construct(WeatherForecast $forecastWeather,Countries $country,ErrorResponse $errMsg)
    {
        $this->forecastWeather = $forecastWeather;
        $this->country = $country;
        $this->errMsg = $errMsg;
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

            if (empty($name)) {
                return $this->json($this->errMsg->errResponse(['field' => 'name','code' => 51]));
            } else {
                $checkNameExist = $doctrine->getRepository(User::class)->findOneBy(['name' => $name]);
                if(!empty($checkNameExist)) {
                    return $this->json($this->errMsg->errResponse(['field' => 'name','code' => 409]));
                }
            }

            if (empty($email)) {
                return $this->json($this->errMsg->errResponse(['field' => 'email','code' => 51]));
            } else {
                $checkEmailExist = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
                if(!empty($checkEmailExist)) {
                    return $this->json($this->errMsg->errResponse(['field' => 'email','code' => 409]));
                }
            }

            if (empty($city)) {
                return $this->json($this->errMsg->errResponse(['field' => 'city','code' => 51]));
            }

            if (empty($country)) {
                return $this->json($this->errMsg->errResponse(['field' => 'country','code' => 51]));
            }

            $countryCode = $this->country->countryCode($country);

            if (empty($countryCode)) {
                return $this->json($this->errMsg->errResponse(['field' => 'country','code' => 404]));
            }

            $plaintextPassword = $decoded->password;
            
            if (empty($plaintextPassword)) {
                return $this->json($this->errMsg->errResponse(['field' => 'password','code' => 51]));
            }

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
            $user->setCountry($countryCode);

            $averageTemperature = $this->forecastWeather->calcTemperature([
                'city' => $city,
                'country' => $countryCode
            ]);

            $user->setTemperature($averageTemperature);
            $em->persist($user);
            $em->flush();

            return $this->json([
                "code" => 200,
                "status" => true,
                "message" => "The current weather forecast {$averageTemperature} ",
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
