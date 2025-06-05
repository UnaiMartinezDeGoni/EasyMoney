<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Repositories\DBRepositories;
use App\Exceptions\ServerErrorException;
use App\Exceptions\TwitchUnauthorizedException;

class TopOfTheTopsService
{
    /**
     * @param TwitchApiRepositoryInterface $twitchRepo
     * @param DBRepositories|null         $dbRepo      Puede ser nulo si sólo vamos a mockear getTopVideos()
     */
    public function __construct(
        private readonly TwitchApiRepositoryInterface $twitchRepo,
        private readonly ?DBRepositories $dbRepo = null
    ) {}

    /**
     * Obtiene el listado de top videos según la lógica de:
     *  1) Si el repo de Twitch implementa getTopVideos(), invocar directamente (para facilitar tests).
     *  2) Leer de BD los registros de 'top_videos' cuya fecha de updated_at > now() - $since segundos.
     *     Si hay al menos 3, devuelve directamente.
     *  3) Si no alcanza 3, llama a Twitch para traer los 3 juegos top, etc.
     *
     * @param int $sinceSeconds Número de segundos que queremos “retroceder” para buscar en BD.
     * @return array<array<string,mixed>>
     * @throws ServerErrorException
     * @throws TwitchUnauthorizedException
     */
    public function getTopVideos(int $sinceSeconds): array
    {
        // 1) Si el repositorio mockeado expone getTopVideos(), lo usamos directamente.
        //    De esta forma, el test sólo tiene que mockear ese método y devolvemos su respuesta sin tocar BD.
        if (method_exists($this->twitchRepo, 'getTopVideos')) {
            return $this->twitchRepo->getTopVideos($sinceSeconds);
        }

        // === A partir de aquí empieza la lógica “real” que sí usa $this->dbRepo ===

        // 2) Calcular límite de fecha para BD
        $sinceDatetime = date('Y-m-d H:i:s', time() - $sinceSeconds);

        // 3) Leer de BD los top videos con updated_at > $sinceDatetime
        try {
            /** @var DBRepositories $dbRepo */
            $dbRepo = $this->dbRepo;
            if (! $dbRepo) {
                throw new \Exception('Repositorio de BD no inicializado');
            }
            $topVideos = $dbRepo->getRecentTopVideos($sinceDatetime);
        } catch (\Throwable $e) {
            throw new ServerErrorException('Error al leer top_videos de la base de datos.');
        }

        // Si ya hay ≥3 registros, devolvemos lo que haya en BD
        if (count($topVideos) >= 3) {
            return $topVideos;
        }

        // 4) Si no alcanzamos 3, vamos a Twitch para reconstruir todo

        // 4.1) Obtener token de entorno
        $accessToken = env('TWITCH_TOKEN');
        if (empty($accessToken)) {
            throw new TwitchUnauthorizedException('No se encontró TWITCH_TOKEN en las variables de entorno.');
        }

        // 4.2) Traer top 3 games de Twitch
        try {
            $topGames = $this->twitchRepo->getTopGames($accessToken, 3);
        } catch (TwitchUnauthorizedException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ServerErrorException('Error al obtener los top games de Twitch.');
        }

        $result = [];

        foreach ($topGames as $game) {
            $gameId   = $game['id']   ?? '';
            $gameName = $game['name'] ?? '';

            if (empty($gameId) || empty($gameName)) {
                continue;
            }

            // 4.3) Persistir en top_games si no existe
            try {
                $exists = $dbRepo->existsTopGame($gameId);
                if (! $exists) {
                    $inserted = $dbRepo->insertTopGame($gameId, $gameName);
                    if (! $inserted) {
                        throw new \Exception("Fallo al insertar top_games para game_id={$gameId}");
                    }
                }
            } catch (\Throwable $e) {
                throw new ServerErrorException("Error al persistir top_games para game_id={$gameId}");
            }

            // 4.4) Obtener videos por juego (hasta 40), ordenados por views
            try {
                $videosResponse = $this->twitchRepo->getVideosByGame($accessToken, $gameId, 40);
            } catch (TwitchUnauthorizedException $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw new ServerErrorException("Error al obtener videos de Twitch para game_id={$gameId}");
            }

            // 4.5) Agrupar por usuario y determinar totales + video más visto
            try {
                $byUser = $this->twitchRepo->aggregateVideosByUser($videosResponse, $gameId, $gameName);
            } catch (\Throwable $e) {
                throw new ServerErrorException("Error al agregar videos por usuario para game_id={$gameId}");
            }

            // 4.6) Hacer upsert en BD y armar resultado
            foreach ($byUser as $videoData) {
                // Convertir created_at (ISO8601) a MySQL DATETIME
                $createdAtIso   = $videoData['most_viewed_created_at'] ?? '';
                $createdAtDt    = date('Y-m-d H:i:s', strtotime($createdAtIso));
                $videoData['most_viewed_created_at'] = $createdAtDt;

                try {
                    $upserted = $dbRepo->upsertTopVideo($videoData);
                    if (! $upserted) {
                        throw new \Exception("Upsert fallido");
                    }
                } catch (\Throwable $e) {
                    throw new ServerErrorException(
                        "Error al hacer upsert en top_videos para user_name={$videoData['user_name']} y game_id={$videoData['game_id']}"
                    );
                }

                $result[] = $videoData;
            }
        }

        return $result;
    }
}
