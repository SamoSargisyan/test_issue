<?php

namespace App\Http\Resources\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="TokenResource",
 *     @OA\Property(property="token_type", type="string", example="Bearer"),
 *     @OA\Property(property="expires_in", type="integer", example="1284000"),
 *     @OA\Property(property="access_token", type="string", example="Большой и длинный токен"),
 *     @OA\Property(property="refresh_token", type="string", example="Большой и длинный токен"),
 * )
 */
class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'token_type' => $this->token_type,
            'expires_in' => $this->expires_in,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
        ];
    }
}
