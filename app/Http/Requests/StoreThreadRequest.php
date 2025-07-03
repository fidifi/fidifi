<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $forum = $this->route('forum');
        
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('threads')->where(function ($query) use ($forum) {
                    return $query->where('forum_id', $forum->id);
                }),
            ],
            'content' => 'required|string|min:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.unique' => 'A thread with this title already exists in this forum.',
            'content.min' => 'The thread content must be at least 10 characters.',
        ];
    }
}