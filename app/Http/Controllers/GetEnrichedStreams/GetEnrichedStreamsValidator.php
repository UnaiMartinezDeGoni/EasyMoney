<?php

declare(strict_types=1);

namespace App\Http\Controllers\GetEnrichedStreams;

use App\Exceptions\InvalidLimitException;


/**
 * Validador dedicado únicamente al parámetro "limit" del endpoint /analytics/streams/enriched.
 *
 * Separa la responsabilidad de validación del controlador para facilitar tests
 * unitarios e inyección de dependencias.
 */
class GetEnrichedStreamsValidator
{
    private const MAX_LIMIT = 20;

    /**
     * Valida y sanitiza el parámetro "limit".
     *
     * @param string|null $limit
     * @return int  Límite validado y convertido a entero.
     *
     * @throws InvalidLimitException Si el parámetro no es válido.
     */
    public function validateLimit(?string $limit): int
    {
        // Debe existir y ser numérico
        if ($limit === null || !is_numeric($limit)) {
            throw new InvalidLimitException('The "limit" parameter must be a positive integer.');
        }

        // Sanitizar contra XSS/inyecciones tontas
        $clean = filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
        if ($clean === '' || (int) $clean < 1) {
            throw new InvalidLimitException('The "limit" parameter must be ≥ 1.');
        }

        $value = (int) $clean;

        if ($value > self::MAX_LIMIT) {
            throw new InvalidLimitException(sprintf('The "limit" parameter must be ≤ %d.', self::MAX_LIMIT));
        }

        return $value;
    }
}
