<?php
namespace App\Services;

use App\Interfaces\TwitchApiRepositoryInterface;
use App\Exceptions\TwitchUnauthorizedException;
use App\Exceptions\ServerErrorException;
use Throwable;

class GetStreamAnalyticsService
{
    public function __construct(
        private readonly TwitchApiRepositoryInterface $repo
    ) {}
    public function listarStreams(): array
    {
        try {
            return $this->repo->getStreams();
        }catch (TwitchUnauthorizedException $e) {
            throw new TwitchUnauthorizedException();
        }  catch (Throwable $e) {
            throw new ServerErrorException();
        }
    }
}
