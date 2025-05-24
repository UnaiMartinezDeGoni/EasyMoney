<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    public function getStreams(): array;

    /**
     * Obtiene la información cruda de un streamer por su ID.
     *
     * @param  string  $id  ID numérico del streamer
     * @return array        Datos crudos de Twitch (o array vacío si no se encuentra).
     */
    public function getStreamerById(string $id): array;
}
