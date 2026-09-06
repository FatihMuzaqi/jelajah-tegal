<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['actor', 'mitra'])->latest('created_at');

        if ($search = trim($request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhere('auditable_id', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('actor', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mitra', function ($mq) use ($search) {
                      $mq->where('display_name', 'like', "%{$search}%")
                         ->orWhere('legal_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($event = trim($request->input('event', ''))) {
            $query->where('event', $event);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
