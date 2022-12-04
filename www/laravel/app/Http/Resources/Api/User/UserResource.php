<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @OA\Schema(schema="UserResource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="full_name", type="string", example="name"),
     *     @OA\Property(property="email", type="string", example="email@mail.com"),
     *     @OA\Property(property="status", type="string", example="active"),
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'status' => $this->status,
        ];
    }
}
