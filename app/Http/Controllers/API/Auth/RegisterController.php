<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
           
            $user = $this->userRepository->create($request->validated());

            $token = auth('api')->login($user);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return ResponseService::sendResponseSuccess([
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'bearer',
            ], Response::HTTP_CREATED, __("messages.User registered successfully"));
        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
            return ResponseService::sendBadRequest(__("messages.User registration failed"));
        }
    }
}
