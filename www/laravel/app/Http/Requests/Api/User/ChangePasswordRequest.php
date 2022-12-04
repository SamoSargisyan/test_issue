<?php

namespace App\Http\Requests\Api\User;

use App\Rules\OnlyLatinCharacterAndNumbers;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserChangePasswordRequest",
 *     required={"old_password", "new_password", "password_confirmation"},
 *     @OA\Property(property="old_password", type="string", example="old_password", minLength=6, maxLength=255),
 *     @OA\Property(property="new_password", type="string", example="new_password", minLength=6, maxLength=255),
 *     @OA\Property(property="new_password_confirmation", type="string", example="new_password", minLength=8, maxLength=20),
 * )
 */
class ChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => [
                'required',
                'string',
                new OnlyLatinCharacterAndNumbers(),
                'min:6',
                'max:255',
            ],
            'new_password' => [
                'required',
                'string',
                new OnlyLatinCharacterAndNumbers(),
                'min:6',
                'max:255',
                'confirmed',
            ],
        ];
    }
}
