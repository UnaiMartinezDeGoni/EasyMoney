<?php

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\InvalidLimitException;

class GetEnrichedStreamsValidator
{
    public function validate(?string $limit): string
    {
        if ($limit === null || trim($limit) === '') {
            throw new InvalidLimitException();
        }

        if (! is_numeric($limit)) {
            throw new InvalidLimitException();
        }

        $clean = filter_var(trim($limit), FILTER_SANITIZE_NUMBER_INT);
        if ($clean === '' || ! is_numeric($clean)) {
            throw new InvalidLimitException();
        }

        $intValue = (int) $clean;
        if ($intValue <= 0 || $intValue > 20) {
            throw new InvalidLimitException();
        }

        return (string) $intValue;
    }
}

