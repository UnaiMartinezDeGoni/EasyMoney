<?php

namespace App\Repositories;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwitchApiRepository implements TwitchApiRepositoryInterface
{
    public function getStreams(): array
    {
        $defaultFirst = (int)env('TWITCH_DEFAULT_FIRST', 20);

        $response = Http::withHeaders([
            'Client-ID' => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
        ])->get('https://api.twitch.tv/helix/streams', [
            'first' => $defaultFirst,
        ]);

        if (!$response->successful()) {
            return [];
        }

        $body = $response->json();
        return $body['data'] ?? [];
    }

    public function getStreamerById(string $id): array
    {
        $response = Http::withHeaders([
            'Client-ID' => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
        ])->get('https://api.twitch.tv/helix/users', [
            'id' => $id,
        ]);

        if (!$response->successful()) {
            return [];
        }

        $body = $response->json();
        return $body['data'][0] ?? [];
    }
    public function getTopGames(string $access_token, int $limit = 3): array
    {
        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . $access_token,
        ])->get('https://api.twitch.tv/helix/games/top', [
            'first' => $limit,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Error obteniendo top games de Twitch (HTTP ' . $response->status() . ')');
        }

        $body = $response->json();
        return $body['data'] ?? [];
    }

    /**
     * Devuelve hasta $limit vídeos ordenados por views para un game_id dado.
     *
     * @param string $access_token
     * @param string $game_id
     * @param int $limit
     * @return array  [ [ 'id'=>…, 'user_id'=>…, 'user_name'=>…, 'view_count'=>…, 'title'=>…, 'duration'=>…, 'created_at'=>… ], … ]
     */
    public function getVideosByGame(string $access_token, string $game_id, int $limit = 40): array
    {
        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . $access_token,
        ])->get('https://api.twitch.tv/helix/videos', [
            'game_id' => $game_id,
            'sort'    => 'views',
            'first'   => $limit,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException("Error obteniendo vídeos para el juego {$game_id} (HTTP " . $response->status() . ")");
        }

        $body = $response->json();
        return $body['data'] ?? [];
    }

    /**
     * Para un array de datos de vídeos (tal como devuelve getVideosByGame()),
     * agrupa por usuario y construye un array con la siguiente estructura por usuario:
     *
     * [
     *   'game_id'                => (string),
     *   'game_name'              => (string),
     *   'user_name'              => (string),
     *   'total_videos'           => (int),
     *   'total_views'            => (int),
     *   'most_viewed_title'      => (string),
     *   'most_viewed_views'      => (int),
     *   'most_viewed_duration'   => (string), // por ejemplo "2h15m30s"
     *   'most_viewed_created_at' => (string), // timestamp ISO8601
     * ]
     *
     * @param array  $videosResponse  Resultado tal cual de la API de Twitch (campo 'data')
     * @param string $game_id
     * @param string $game_name
     * @return array  [ [ …los campos de arriba… ], … ]
     */
    public function aggregateVideosByUser(array $videosResponse, string $game_id, string $game_name): array
    {
        $byUser = [];

        foreach ($videosResponse as $video) {
            $user = $video['user_name'];
            if (! isset($byUser[$user])) {
                $byUser[$user] = [
                    'game_id'                => $game_id,
                    'game_name'              => $game_name,
                    'user_name'              => $user,
                    'total_videos'           => 0,
                    'total_views'            => 0,
                    'most_viewed_title'      => $video['title'],
                    'most_viewed_views'      => $video['view_count'],
                    'most_viewed_duration'   => $video['duration'],
                    'most_viewed_created_at' => $video['created_at'],
                ];
            }

            // Incrementar totales
            $byUser[$user]['total_videos']++;
            $byUser[$user]['total_views'] += $video['view_count'];

            // Actualizar si este video tiene más views que el anterior
            if ($video['view_count'] > $byUser[$user]['most_viewed_views']) {
                $byUser[$user]['most_viewed_title']      = $video['title'];
                $byUser[$user]['most_viewed_views']      = $video['view_count'];
                $byUser[$user]['most_viewed_duration']   = $video['duration'];
                $byUser[$user]['most_viewed_created_at'] = $video['created_at'];
            }
        }

        // Devolver array indexado numéricamente
        return array_values($byUser);
    }
}
