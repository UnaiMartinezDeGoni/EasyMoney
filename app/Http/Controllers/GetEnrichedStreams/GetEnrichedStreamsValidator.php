<?php

declare(strict_types=1);

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\InvalidLimitException;

class GetEnrichedStreamsValidator
{
    private const MIN = 1;
    private const MAX = 20;

    public function validate(?string $limit): string
    {
        if ($limit === null || $limit === '') {
            throw new InvalidLimitException();
        }

        if (!is_numeric($limit)) {
            throw new InvalidLimitException();
        }

        $clean = filter_var(trim($limit), FILTER_SANITIZE_NUMBER_INT);
        if ($clean === '' || !is_numeric($clean)) {
            throw new InvalidLimitException();
        }

        $value = (int) $clean;
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidLimitException();
        }

        return (string) $value;
    }
}
