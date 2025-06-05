<?php

namespace App\Services;

use App\Interfaces\DBRepositoriesInterface;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;
use Throwable;

class TopOfTheTopsService
{

    public function __construct(
        private readonly TwitchApiRepositoryInterface $twitchRepo,
        private readonly ?DBRepositoriesInterface $dbRepo = null
    ) {}

    public function getTopVideos(int $sinceSeconds): array
    {

        $sinceDatetime = date('Y-m-d H:i:s', time() - $sinceSeconds);

        try {

            $topVideos = $this->dbRepo->getRecentTopVideos($sinceDatetime);
        } catch (\Throwable $e) {
            throw new ServerErrorException();
        }

        if (count($topVideos) >= 3) {
            return $topVideos;
        }

        $accessToken = env('TWITCH_TOKEN');


        try {
            $topGames = $this->twitchRepo->getTopGames($accessToken, 3);
        } catch (TwitchUnauthorizedException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ServerErrorException();
        }

        $result = [];

        foreach ($topGames as $game) {
            $gameId   = $game['id']   ?? '';
            $gameName = $game['name'] ?? '';

            if (empty($gameId) || empty($gameName)) {
                continue;
            }

            try {
                $exists = $this->dbRepo->existsTopGame($gameId);
                if (! $exists) {
                    $inserted = $this->dbRepo->insertTopGame($gameId, $gameName);
                    if (! $inserted) {
                        throw new \Exception("Fallo al insertar top_games para game_id={$gameId}");
                    }
                }

            } catch (Throwable $e) {
                throw new ServerErrorException();
            }

            try {
                $videosResponse = $this->twitchRepo->getVideosByGame($accessToken, $gameId, 40);
            } catch (TwitchUnauthorizedException $e) {
                throw $e;

            } catch (Throwable $e) {
                throw new ServerErrorException();
            }

            try {
                $byUser = $this->twitchRepo->aggregateVideosByUser($videosResponse, $gameId, $gameName);

            } catch (Throwable $e) {
                throw new ServerErrorException();
            }

            foreach ($byUser as $videoData) {
                $createdAtIso   = $videoData['most_viewed_created_at'] ?? '';
                $createdAtDt    = date('Y-m-d H:i:s', strtotime($createdAtIso));
                $videoData['most_viewed_created_at'] = $createdAtDt;

                try {
                    $upserted = $this->dbRepo->upsertTopVideo($videoData);
                    if (! $upserted) {
                        throw new \Exception("Upsert fallido");
                    }

                } catch (Throwable $e) {
                    throw new ServerErrorException();
                }

                $result[] = $videoData;
            }
        }

        return $result;
    }
}
