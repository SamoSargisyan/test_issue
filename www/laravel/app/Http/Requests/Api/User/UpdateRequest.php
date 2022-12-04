<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserUpdateRequest",
 *     required={"full_name"},
 *     @OA\Property(property="full_name", type="string", example="Иван", maxLength=60),
 * )
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => [
               'required',
               'string',
               'max:60',
            ],
        ];
    }
}
