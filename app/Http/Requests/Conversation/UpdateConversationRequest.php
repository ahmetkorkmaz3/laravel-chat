<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_group' => [
                'nullable',
                'boolean',
            ],
            'name' => [
                'string',
                'min:3',
                'required_if:is_group,true',
            ],
            'to_user_id' => [
                'nullable',
                'array',
            ],
            'to_user_id.*' => [
                'integer',
                'exists:users,id',
                Rule::notIn([auth()->user()->id]),
            ],
        ];
    }
}
