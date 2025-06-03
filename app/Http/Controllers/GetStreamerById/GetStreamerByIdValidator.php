<?php
declare(strict_types=1);

namespace App\Http\Controllers\GetStreamerById;

use App\Exceptions\EmptyOrInvalidIdException;

class GetStreamerByIdValidator
{
    public function validate(?string $idParam): string
    {
        $id = trim((string)$idParam);
        if ($id === '' || !ctype_digit($id) || (int)$id < 1) {
            throw new EmptyOrInvalidIdException();
        }
        return $id;
    }
}
