<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate($request->get('per_page', 50));

        return response()->json($logs);
    }

    public function show(AuditLog $auditLog)
    {
        return response()->json(['data' => $auditLog->load('user')]);
    }
}
