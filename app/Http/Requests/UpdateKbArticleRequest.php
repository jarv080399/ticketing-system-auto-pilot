<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKbArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'agent']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'excerpt' => 'nullable|string|max:255',
            'visibility' => 'sometimes|required|in:public,internal',
            'status' => 'sometimes|required|in:draft,published,archived',
            'category_id' => 'nullable|exists:kb_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'change_summary' => 'required|string|max:255',
        ];
    }
}
