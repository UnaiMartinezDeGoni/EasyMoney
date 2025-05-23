<?php
namespace App\Http\Controllers\GetStreams;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;

class StreamsValidator
{
    public function __construct(
        private readonly ValidatorFactory $validatorFactory
    ) {}

    /**
     * Valida el parámetro `limit` y devuelve su valor (o 10 por defecto).
     *
     * @param Request $request
     * @return int
     * @throws ValidationException
     */
    public function validate(Request $request): int
    {
        $data = $request->all();

        $validator = $this->validatorFactory->make($data, [
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return isset($data['limit']) ? (int)$data['limit'] : 10;
    }
}
