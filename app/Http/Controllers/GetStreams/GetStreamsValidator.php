<?php

namespace App\Http\Controllers\GetStreams;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;

class GetStreamsValidator
{
    public function __construct(
        private readonly ValidatorFactory $validatorFactory
    ) {}

    /**
     * Valida el request y devuelve los datos saneados.
     *
     * @param Request $request
     * @return array Datos validados con 'limit' (int)
     * @throws ValidationException
     */
    public function validate(Request $request): array
    {
        $rules = [
            'limit' => 'sometimes|integer|min:1|max:100',
        ];

        $data = $request->all();
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            // arroja 422 con el array de errores en JSON
            throw new ValidationException($validator);
        }

        // asigna valor por defecto si no viene
        $data['limit'] = isset($data['limit'])
            ? (int) $data['limit']
            : 10;

        return $data;
    }
}
