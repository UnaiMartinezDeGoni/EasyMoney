<?php

namespace App\Repositories;

use App\Interfaces\TwitchApiRepositoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class TwitchApiRepository implements TwitchApiRepositoryInterface
{
    public function __construct(
        private readonly ClientInterface $http
    ) {}

    public function getStreams(): array
    {
        try {
            $defaultFirst = (int) env('TWITCH_DEFAULT_FIRST', 20);

            $response = $this->http->get('https://api.twitch.tv/helix/streams', [
                'headers' => [
                    'Client-ID'     => env('TWITCH_CLIENT_ID'),
                    'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
                ],
                'query' => ['first' => $defaultFirst],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body['data'] ?? [];
        } catch (GuzzleException $e) {
            // En caso de error de conexión o de la API, devolvemos un array vacío
            return [];
        }
    }
}
