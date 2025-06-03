<?php

namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\StreamerNotFoundException;
use App\Exceptions\ServerErrorException;
use Throwable;

class GetStreamerByIdService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    /**
     * Recupera info cruda del streamer o lanza excepciones:
     * - StreamerNotFoundException si no existe.
     * - ServerErrorException ante cualquier error interno.
     *
     * @param string $id
     * @return array<string, mixed>
     * @throws StreamerNotFoundException
     * @throws ServerErrorException
     */
    public function getStreamerById(string $id): array
    {
        try {
            $data = $this->repo->getStreamerById($id);
        } catch (Throwable $e) {
            throw new ServerErrorException();
        }

        if (empty($data)) {
            throw new StreamerNotFoundException();
        }

        return $data;
    }
}
