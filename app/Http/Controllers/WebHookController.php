<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebHookController extends Controller
{
    /**
     *
     */
    public function index()
    {
        // listen to the POST request from Dialogflow
        $request = file_get_contents("php://input");
        Log::alert($request);
        $requestJson = json_decode($request, true);

        $city = $requestJson['queryResult']['parameters']['geo-city'];
        Log::info("City: $city");

        if (isset($city)) {
            $this->getWeatherInformation($city);
        }
    }
    /**
     * Makes an API call to OpenWeatherMap and
     * retrieves the weather data of a given city.
     *
     * @param string $city
     *
     * @return void
     */
    public function getWeatherInformation($city)
    {
        $apiKey = env("OPEN_WEATHER_MAP_API_KEY");
        $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&units=metric&appid=$apiKey";
        $weather = file_get_contents($weatherUrl);

        $weatherDetails =json_decode($weather, true);

        $temperature = round($weatherDetails["main"]["temp"]);
        Log::info("Temp: $temperature");
        $weatherDescription = $weatherDetails["weather"][0]["description"];

        $this->sendFulfillmentResponse($temperature, $weatherDescription);
    }

    /**
     * @param $temperature
     * @param $weatherDescription
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendFulfillmentResponse($temperature, $weatherDescription)
    {
        $response = "It is $temperature degrees with $weatherDescription";
        Log::info("Weather: $response");

//        response()->json([
//            "fulfillmentText" => $response
//        ]);
        $fulfillment = array(
            "fulfillmentText" => $response
        );

        echo(json_encode($fulfillment));
    }


}
