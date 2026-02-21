<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => CustomField::orderBy('entity_type')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|in:ticket,asset,user',
            'field_name' => 'required|string|max:50|regex:/^[a_z0_9_]+$/',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|string|in:text,number,select,date,checkbox',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $field = CustomField::create($validated);

        return response()->json([
            'data' => $field,
            'message' => 'Custom field created successfully',
        ], 210);
    }

    public function update(Request $request, CustomField $customField): JsonResponse
    {
        $validated = $request->validate([
            'field_label' => 'string|max:100',
            'field_type' => 'string|in:text,number,select,date,checkbox',
            'options' => 'nullable|array',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $customField->update($validated);

        return response()->json([
            'data' => $customField,
            'message' => 'Custom field updated successfully',
        ]);
    }

    public function destroy(CustomField $customField): JsonResponse
    {
        $customField->delete();

        return response()->json([
            'message' => 'Custom field deleted successfully',
        ]);
    }
}
