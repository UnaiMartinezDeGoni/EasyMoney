<?php

namespace App\Repositories;

use App\Interfaces\TwitchApiRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TwitchApiRepository implements TwitchApiRepositoryInterface
{
    protected Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => env('TWITCH_API_URL', 'https://api.twitch.tv/helix/'),
            'timeout'  => 5.0,
        ]);
    }

    public function getStreams(int $limit): array
    {
        try {
            $response = $this->http->get('streams', [
                'headers' => [
                    'Client-ID'     => env('TWITCH_CLIENT_ID'),
                    'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
                ],
                'query' => ['first' => $limit],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (GuzzleException $e) {
            // podrías lanzar una excepción de dominio aquí
            return [];
        }
    }
}
