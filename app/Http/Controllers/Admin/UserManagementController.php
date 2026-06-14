<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    // Daftar user (pencarian & pagination untuk admin)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $role = $request->query('role');
        $perPage = (int) $request->query('per_page', 20);

        $query = User::query();

        if ($q !== '') {
            $query->where(function ($wr) use ($q) {
                $wr->where('username', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('full_name', 'like', "%{$q}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        $users = $query->orderByDesc('id')
                       ->paginate(max(10, min(200, $perPage)))
                       ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'role'));
    }
}
 
