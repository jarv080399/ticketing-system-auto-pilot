<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KbCategory;
use App\Http\Requests\StoreKbCategoryRequest;
use App\Http\Requests\UpdateKbCategoryRequest;
use Illuminate\Support\Str;

class KbCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return structured tree
        $categories = KbCategory::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();

        return response()->json([
            'data' => $categories,
            'message' => 'Categories retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKbCategoryRequest $request)
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);

        $category = KbCategory::create($validated);

        return response()->json([
            'data' => $category,
            'message' => 'Category created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(KbCategory $category)
    {
        $category->load(['children', 'articles']);

        return response()->json([
            'data' => $category,
            'message' => 'Category retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKbCategoryRequest $request, KbCategory $category)
    {
        $validated = $request->validated();
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'data' => $category,
            'message' => 'Category updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KbCategory $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
