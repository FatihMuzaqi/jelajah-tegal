<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->latest('created_at')->paginate(20);

        return view('admin.audit.index', compact('logs'));
    }
}
