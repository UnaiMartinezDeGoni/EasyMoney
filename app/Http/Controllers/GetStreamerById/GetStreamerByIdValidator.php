<?php

namespace App\Http\Controllers\GetStreamerById;

use App\Exceptions\EmptyOrInvalidIdException;

/**
 * Valida y limpia el parámetro "id" para el endpoint GetStreamerById.
 */
class GetStreamerByIdValidator
{
    /**
     * @param  mixed  $idParam
     * @return string ID limpio y validado
     * @throws EmptyOrInvalidIdException
     */
    public function validate(?string $idParam): string
    {
        $id = trim((string) $idParam);
        if ($id === '' || !ctype_digit($id) || (int) $id < 1) {
            throw new EmptyOrInvalidIdException("Invalid or missing 'id' parameter.");
        }
        return $id;
    }
}
