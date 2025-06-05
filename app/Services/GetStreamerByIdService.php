<?php
namespace App\Services;

use App\Exceptions\TwitchUnauthorizedException;
use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\StreamerNotFoundException;
use App\Exceptions\ServerErrorException;
use Throwable;

class GetStreamerByIdService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}

    public function getStreamerById(string $id): array
    {
        try {
            $data = $this->repo->getStreamerById($id);
        }catch (TwitchUnauthorizedException $e) {
            throw new TwitchUnauthorizedException();
        }  catch (Throwable $e) {
            throw new ServerErrorException();
        }

        if (empty($data)) {
            throw new StreamerNotFoundException();
        }

        return $data;
    }
}
