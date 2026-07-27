<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Company;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function admin(Request $request)
    {
        $companyId = $request->company_id;
        $action    = $request->action;

        $logs = ActivityLog::with('user')
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($action,    fn ($q) => $q->where('action', 'like', "%{$action}%"))
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.activity-log', compact('logs', 'companies', 'companyId', 'action'));
    }

    public function tenant(Request $request)
    {
        $user      = auth()->user();
        $action    = $request->action;

        $logs = ActivityLog::with('user')
            ->where('company_id', $user->company_id)
            ->when($action, fn ($q) => $q->where('action', 'like', "%{$action}%"))
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('activity-log', compact('logs', 'action'));
    }
}
