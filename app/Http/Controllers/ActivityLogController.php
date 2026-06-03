<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->recent()->paginate(20);

        // Get distinct model types for filter dropdown
        $modelTypes = ActivityLog::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        return view('activity-logs.index', compact('logs', 'modelTypes'));
    }
}
