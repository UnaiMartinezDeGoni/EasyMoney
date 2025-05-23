<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    /**
     * Obtiene los streams desde la API de Twitch
     *
     * @param int $limit Número máximo de streams a obtener
     * @return array Lista de streams como arrays asociativos
     */
    public function getStreams(int $limit): array;
}
