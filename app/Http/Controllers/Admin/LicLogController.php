<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicLog;
use Illuminate\Http\Request;

class LicLogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        $level = $request->get('level');
        $search = $request->get('search');
        
        $logs = LicLog::with(['license', 'user'])
            ->when($level, function ($query, $level) {
                $query->where('level', $level);
            })
            ->when($search, function ($query, $search) {
                $query->where('message', 'like', "%{$search}%");
            })
            ->latest('created_at')
            ->paginate(30);

        return view('admin.logs.index', compact('logs', 'level', 'search'));
    }
}
