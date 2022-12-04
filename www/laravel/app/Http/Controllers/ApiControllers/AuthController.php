<?php

namespace App\Http\Controllers\ApiControllers;

use App\Enums\UserStatus;
use App\Exceptions\WrongCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\SigninRequest;
use App\Http\Requests\Api\Auth\SignupRequest;
use App\Http\Resources\Api\Auth\SigninResource;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/signup",
     *     summary="Регистрация",
     *     tags={"Регистрация"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SignupRequest"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Success",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ValidationException")
     *         )
     *     ),
     * )
     */
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        $data = array_merge($data, [
            'password' => Hash::make($data['password']),
            'status' => UserStatus::active,
        ]);

        User::create($data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/signin",
     *     summary="Вход",
     *     tags={"Вход"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SigninRequest"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успех",
     *         @OA\JsonContent(ref="#/components/schemas/SigninResource"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ValidationException")
     *         )
     *     ),
     * )
     */
    public function signin(SigninRequest $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            throw new WrongCredentialsException();
        }

        $token = $user->createTokenWithRefresh($request->password);

        return response()
            ->json(SigninResource::make([
                'user' => $user,
                'token' => $token
            ]), Response::HTTP_CREATED);
    }
}
