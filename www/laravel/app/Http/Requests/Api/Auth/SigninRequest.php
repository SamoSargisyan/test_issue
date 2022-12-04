<?php

namespace App\Http\Requests\Api\Auth;

use App\Rules\OnlyLatinCharacterAndNumbers;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="SigninRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", example="username@mail.ru"),
 *     @OA\Property(property="password", type="string", example="p@ssw0rd"),
 * )
 */
class SigninRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email,deleted_at,NULL',
            'password' => [
                'required',
                'string',
                new OnlyLatinCharacterAndNumbers(),
                'min:6',
                'max:255',
            ],
        ];
    }
}
