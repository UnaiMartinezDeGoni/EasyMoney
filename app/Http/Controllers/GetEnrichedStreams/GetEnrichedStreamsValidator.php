<?php
// file: app/Http/Controllers/GetEnrichedStreams/GetEnrichedStreamsValidator.php
declare(strict_types=1);

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\InvalidLimitException;

class GetEnrichedStreamsValidator
{
    private const MIN = 1;
    private const MAX = 20;

    public function validate(?string $limit): string
    {
        // ① Si falta o está vacío → excepción inmediata
        if ($limit === null || $limit === '') {
            throw new InvalidLimitException();
        }

        // ② Si no es numérico
        if (!is_numeric($limit)) {
            throw new InvalidLimitException();
        }

        // ③ Sanitizar
        $clean = filter_var(trim($limit), FILTER_SANITIZE_NUMBER_INT);
        if ($clean === '' || !is_numeric($clean)) {
            throw new InvalidLimitException();
        }

        // ④ Rango permitido
        $value = (int) $clean;
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidLimitException();
        }

        return (string) $value;
    }

}
