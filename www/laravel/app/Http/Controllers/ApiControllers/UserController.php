<?php

namespace App\Http\Controllers\ApiControllers;

use App\Exceptions\WrongCredentialsException;
use App\Http\Requests\Api\User\ChangePasswordRequest;
use App\Http\Requests\Api\User\ShowRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * @OA\Get(
     *     path="/api/v1/profile/show",
     *     summary="Получить пользователя",
     *     tags={"Профиль"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1),
     *         description="ID пользователя"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успех",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AuthorizationException")
     *         )
     *     ),
     * )
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $user = User::find($request->id);

        return response()->json(UserResource::make($user));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile/update",
     *     summary="Обновить свой профиль",
     *     tags={"Профиль"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успех",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AuthorizationException")
     *         )
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
    public function update(UpdateRequest $request)
    {
        $user = Auth::user();
        $user->update($request->validated());

        return response()->json(UserResource::make($user));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/profile/change_password",
     *     summary="Обновить пароль",
     *     tags={"Профиль"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserChangePasswordRequest"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Успех",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AuthorizationException")
     *         )
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
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            throw new WrongCredentialsException();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/profile/delete",
     *     summary="Удалить свой профиль",
     *     tags={"Профиль"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *         response=204,
     *         description="Успех",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AuthorizationException")
     *         )
     *     ),
     * )
     */
    public function delete()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
