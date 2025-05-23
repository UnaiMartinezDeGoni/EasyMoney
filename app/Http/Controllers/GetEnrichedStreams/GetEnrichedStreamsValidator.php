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
    private const MIN = 1;   // Incluyente
    private const MAX = 20;

    /**
     * @throws InvalidLimitException Si el parámetro no es un número dentro de rango.
     */
    public function validate(?string $limit): string
    {
        // 1. Debe suministrarse y ser numérico
        if ($limit === null || !is_numeric($limit)) {
            throw new InvalidLimitException();
        }

        // 2. Saneamos rápidamente y retiramos etiquetas/inyección
        $trimmed = trim($limit);
        $clean   = filter_var($trimmed, FILTER_SANITIZE_NUMBER_INT);

        if ($clean === '') {
            throw new InvalidLimitException();
        }

        // 3. Rango permitido MIN‑MAX
        $value = (int) $clean;
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidLimitException();
        }

        return (string) $value;
    }
}
