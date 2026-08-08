<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connection' => config('database.default'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'Aktif' : 'Non-Aktif',
            'timezone' => config('app.timezone'),
        ];

        return view('super-admin.settings.index', compact('health'));
    }
}
