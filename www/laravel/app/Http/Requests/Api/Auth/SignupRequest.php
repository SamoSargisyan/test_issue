<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\OnlyLatinCharacterAndNumbers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(schema="SignupRequest",
 *     required={"full_name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="full_name", type="string", nullable=true, example="name"),
 *     @OA\Property(property="email", type="string", example="email@mail.com"),
 *     @OA\Property(property="password", type="string", example="p@ssw0rd"),
 *     @OA\Property(property="password_confirmation", type="string", example="p@ssw0rd"),
 * )
 */
class SignupRequest extends FormRequest
{
    public function rules()
    {
        return [
            'full_name' => [
                'required',
                'string',
                'max:60',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
            ],
            'password' => [
                'required',
                'confirmed',
                'string',
                new OnlyLatinCharacterAndNumbers(),
                'min:6',
                'max:255',
            ],
        ];
    }
}
