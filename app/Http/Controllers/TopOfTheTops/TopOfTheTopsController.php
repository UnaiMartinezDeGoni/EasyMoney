<?php

namespace App\Http\Controllers\TopOfTheTops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TopOfTheTopsService;
use App\Exceptions\InvalidSinceParameterException;
use App\Exceptions\TopOfTheTopsServerException;
use Throwable;

class TopOfTheTopsController extends Controller
{
    public function __construct(
        private TopOfTheTopsService $topOfTheTopsService
    ) {}

    public function index(Request $request)
    {
        $data = $request->query();
        $validator = new TopOfTheTopsValidator();
        try {
            $validator->validate($data);
        } catch (InvalidSinceParameterException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                $e->getCode(),
                [],
                JSON_PRETTY_PRINT
            );
        }

        $since = isset($data['since']) ? (int)$data['since'] : 600;

        try {
            $response = $this->topOfTheTopsService->getTopVideos($since);
        } catch (Throwable $e) {
            \Log::error('[TopOfTheTops] ' . $e->getMessage());
            // Podrías revisar el tipo de excepción y, si es una excepcion interna, lanzar TopOfTheTopsServerException
            throw new ServerErrorException();
        }

        return $response;
    }
}
