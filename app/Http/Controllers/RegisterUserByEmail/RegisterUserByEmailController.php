<?php

namespace App\Http\Controllers\RegisterUserByEmail;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;
use App\Repositories\DB_Repositories;

class RegisterUserByEmailController extends Controller
{
    protected $dbRepo;

    public function __construct()
    {
        require_once __DIR__ . '/../../../../funcionesComunes.php';
        $this->dbRepo = new DB_Repositories();
    }

    public function index(Request $request)
    {
        $data = $request->json()->all();
        $validator = new RegisterUserByEmailValidator();

        try {
            $validator->validate($data);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                400,
                [],
                JSON_PRETTY_PRINT
            );
        }

        $email  = $data['email'];
        $apiKey = generateApiKey();

        try {
            $user = $this->dbRepo->findUserByEmail($email);

            if ($user) {
                $this->dbRepo->updateApiKey($email, $apiKey);
            } else {
                $this->dbRepo->insertUser($email, $apiKey);
            }

            return response()->json(
                ["api_key" => $apiKey],
                200,
                [],
                JSON_PRETTY_PRINT
            );
        } catch (Throwable $e) {
            $serverError = new ServerErrorException();
            return response()->json(
                ['error' => $serverError->getMessage()],
                500,
                [],
                JSON_PRETTY_PRINT
            );
        }
    }
}
