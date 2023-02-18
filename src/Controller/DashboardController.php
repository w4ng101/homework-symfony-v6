<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ApiCall;

#[Route('/api', name: 'api_')]
class DashboardController extends AbstractController
{
    private $api;

    public function __construct(ApiCall $api)
    {
        $this->api = $api;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): JsonResponse
    {
        try {
            $api_openweathermap_response = $this->api->apiCallCurl('openweathermap');
            $api_weatherapi_response = $this->api->apiCallCurl('weatherapi');
            $average_temperature = $this->api->averageCalc($api_openweathermap_response['main']['temp'],$api_weatherapi_response['temp_f']);
            return $this->json([
                'message' => 'Welcome to your new controller!',
                'path' => 'src/Controller/DashboardController.php',
                'temperature' => $average_temperature,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
