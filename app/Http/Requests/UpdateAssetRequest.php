<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'agent']);
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')->id ?? $this->route('asset');

        return [
            'serial_number' => ['sometimes', 'required', 'string', Rule::unique('assets')->ignore($assetId)],
            'asset_tag' => ['sometimes', 'nullable', 'string', Rule::unique('assets')->ignore($assetId)],
            'type' => 'sometimes|required|in:laptop,desktop,monitor,printer,phone,license,peripheral,other',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,in_repair,retired,disposed,in_stock',
            'owner_user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'warranty_expires_at' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
