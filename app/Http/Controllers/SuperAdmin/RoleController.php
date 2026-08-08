<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        $permissions = Permission::all();

        return view('super-admin.roles.index', compact('roles', 'permissions'));
    }

    public function permissions()
    {
        $permissions = Permission::all();

        return view('super-admin.permissions.index', compact('permissions'));
    }
}
