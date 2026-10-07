<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->query('module');
        $action = $request->query('action');
        $search = $request->query('search');

        $query = AuditLog::query();

        if ($module && $module !== 'all') {
            $query->where('module', $module);
        }
        if ($action && $action !== 'all') {
            $query->where('action', $action);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('kode_log', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $modules = AuditLog::select('module')->distinct()->pluck('module');
        $actions = ['CREATE', 'UPDATE', 'DELETE', 'STATUS_CHANGE'];

        return view('audit.index', compact('logs', 'modules', 'actions'));
    }
}
