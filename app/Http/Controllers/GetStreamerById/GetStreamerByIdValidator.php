<?php
// app/Http/Controllers/GetStreamerById/GetStreamerByIdValidator.php

namespace App\Http\Controllers\GetStreamerById;

use App\Exceptions\EmptyOrInvalidIdException;

class GetStreamerByIdValidator
{
    /**
     * @param  mixed  $idParam
     * @return string
     * @throws EmptyOrInvalidIdException
     */
    public function validate(?string $idParam): string
    {
        $id = trim((string)$idParam);
        if ($id === '' || !ctype_digit($id) || (int)$id < 1) {
            throw new EmptyOrInvalidIdException();
        }
        return $id;
    }
}
