<?php

namespace App\Interfaces;

interface TwitchApiRepositoryInterface
{
    public function getStreams(): array;
}
