<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Services\WeatherForecast;
use App\Common\Cache;


#[Route('/api', name: 'api_')]
class DashboardController extends AbstractController
{
    private $forecastWeather;
    private $cached;

    public function __construct(WeatherForecast $forecastWeather,Cache $cached)
    {
        $this->forecastWeather = $forecastWeather;
        $this->cached = $cached;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): JsonResponse
    {
        $city = 'Butuan City';
        $country = 'ph';
        $params = [
            'city' => $city,
            'country' => $country
        ];
        try {
            return $this->json([
                'message' => 'Welcome the weather forecast temperature '.$this->forecastWeather->calcTemperature($params).' !',
                'users_list' => $this->cached->UserList(),
                'path' => 'src/Controller/DashboardController.php',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
