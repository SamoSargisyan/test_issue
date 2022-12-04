<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UserShowRequest",
 *     required={"id"},
 *     @OA\Property(property="id", type="integer", example=1),
 * )
 */
class ShowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:users,id,deleted_at,NULL',
        ];
    }
}
