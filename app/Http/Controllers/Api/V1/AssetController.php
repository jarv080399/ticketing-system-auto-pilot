<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\User;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel; // Assuming regular Excel/CSV handling

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('owner');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('serial_number', 'like', "%$search%")
                  ->orWhere('asset_tag', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%")
                  ->orWhere('manufacturer', 'like', "%$search%");
            });
        }

        if ($request->has('owner_id')) {
            $query->where('owner_user_id', $request->owner_id);
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->validated());

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'created',
            'performed_by' => auth()->id(),
            'new_value' => json_encode($asset->only(['status', 'owner_user_id'])),
        ]);

        return response()->json([
            'message' => 'Asset created successfully',
            'data' => $asset
        ], 201);
    }

    public function show(Asset $asset)
    {
        return $asset->load('owner', 'history.performer');
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $oldValue = $asset->only(['status', 'owner_user_id', 'location']);
        $asset->update($request->validated());

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'edited',
            'performed_by' => auth()->id(),
            'old_value' => json_encode($oldValue),
            'new_value' => json_encode($asset->only(['status', 'owner_user_id', 'location'])),
        ]);

        return response()->json([
            'message' => 'Asset updated successfully',
            'data' => $asset
        ]);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return response()->json(['message' => 'Asset deleted successfully']);
    }

    public function assign(Request $request, Asset $asset)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $oldOwner = $asset->owner_user_id;
        $asset->update([
            'owner_user_id' => $request->user_id,
            'status' => 'active'
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'assigned',
            'performed_by' => auth()->id(),
            'old_value' => $oldOwner,
            'new_value' => $request->user_id,
        ]);

        return response()->json(['message' => 'Asset assigned successfully', 'data' => $asset]);
    }

    public function unassign(Asset $asset)
    {
        $oldOwner = $asset->owner_user_id;
        $asset->update([
            'owner_user_id' => null,
            'status' => 'in_stock'
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'action' => 'unassigned',
            'performed_by' => auth()->id(),
            'old_value' => $oldOwner,
        ]);

        return response()->json(['message' => 'Asset unassigned successfully', 'data' => $asset]);
    }

    public function history(Asset $asset)
    {
        return $asset->history()->with('performer')->get();
    }

    public function userAssets(Request $request)
    {
        $assets = $request->user()->assets()->with('owner')->paginate(15);

        return response()->json([
            'data' => $assets->items(),
            'total' => $assets->total(),
        ]);
    }
}
