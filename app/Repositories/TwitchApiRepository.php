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

        return array_values($byUser);
    }
}
