<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->has('action')) {
            $query->where('action', 'like', '%' . $request->get('action') . '%');
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->has('type')) {
            $query->where('auditable_type', 'like', '%' . $request->get('type') . '%');
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                  ->orWhere('auditable_type', 'like', "%$search%")
                  ->orWhere('auditable_id', 'like', "%$search%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return response()->json($logs);
    }

    public function show(AuditLog $auditLog)
    {
        return response()->json(['data' => $auditLog->load('user')]);
    }
}
