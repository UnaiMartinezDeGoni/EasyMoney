<?php

namespace App\Repositories;

use App\Interfaces\TwitchApiRepositoryInterface;
use Illuminate\Support\Facades\Http;

class TwitchApiRepository implements TwitchApiRepositoryInterface
{
    public function getStreams(): array
    {
        $defaultFirst = (int) env('TWITCH_DEFAULT_FIRST', 20);

        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
        ])->get('https://api.twitch.tv/helix/streams', [
            'first' => $defaultFirst,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $body = $response->json();
        return $body['data'] ?? [];
    }

    public function getStreamerById(string $id): array
    {
        $response = Http::withHeaders([
            'Client-ID'     => env('TWITCH_CLIENT_ID'),
            'Authorization' => 'Bearer ' . env('TWITCH_TOKEN'),
        ])->get('https://api.twitch.tv/helix/users', [
            'id' => $id,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $body = $response->json();
        return $body['data'][0] ?? [];
    }
}
