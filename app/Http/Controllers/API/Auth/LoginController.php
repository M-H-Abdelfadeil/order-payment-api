<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest as AuthLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !auth()->attempt($credentials)) {
            Log::warning('Login failed', ['email' => $credentials['email']]);
            return ResponseService::sendUnauthorized(__('messages.Incorrect email or password'));
        }

        $token = auth('api')->login($user);

        Log::info('User logged in', ['user_id' => $user->id]);

        return ResponseService::sendResponseSuccess([
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'bearer',
        ], Response::HTTP_OK, __('messages.Login successful'));
    }
}
