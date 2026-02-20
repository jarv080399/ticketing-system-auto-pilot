<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'agent']);
    }

    public function rules(): array
    {
        return [
            'serial_number' => 'required|string|unique:assets,serial_number',
            'asset_tag' => 'nullable|string|unique:assets,asset_tag',
            'type' => 'required|in:laptop,desktop,monitor,printer,phone,license,peripheral,other',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,in_repair,retired,disposed,in_stock',
            'owner_user_id' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'warranty_expires_at' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
