<?php

namespace App\Http\Resources\Api\Auth;

use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SigninResource extends JsonResource
{
    /**
     * @OA\Schema(schema="SigninResource",
     *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *     @OA\Property(property="token", ref="#/components/schemas/TokenResource")
     * )
     */
    public function toArray($request)
    {
        return [
            'user' => UserResource::make($this['user']),
            'token' => TokenResource::make($this['token']),
        ];
    }
}
