<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    public function getStreams(): array;


    public function getStreamerById(string $id): array;
}
