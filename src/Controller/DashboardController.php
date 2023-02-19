<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\ForecastWeather;

#[Route('/api', name: 'api_')]
class DashboardController extends AbstractController
{
    private $forecastWeather;

    public function __construct(ApiCall $api,ForecastWeather $forecastWeather)
    {
        $this->forecastWeather = $forecastWeather;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): JsonResponse
    {
        try {
            return $this->json([
                'message' => 'Welcome the weather forecast '.$this->forecastWeather->calcTemperature().' !',
                'path' => 'src/Controller/DashboardController.php',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
