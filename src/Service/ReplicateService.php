<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReplicateService
{
    private Client $client;
    private string $apiToken;

    public function __construct(string $apiToken)
    {
        $this->client = new Client();
        $this->apiToken = $apiToken;
    }

    public function processVideo(string $photoPath, string $videoPath): string
    {
        $response = $this->client->request('POST', 'https://api.replicate.com/v1/predictions', [
            'headers' => [
                'Authorization' => 'Token ' . $this->apiToken,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'version' => 'YOUR_MODEL_VERSION',
                'input' => [
                    'photo' => base64_encode(file_get_contents($photoPath)),
                    'video' => base64_encode(file_get_contents($videoPath))
                ]
            ]
        ]);

        $responseData = $response->toArray();
        return $responseData['output']['result_url'];
    }
}