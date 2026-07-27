<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $superadmins = User::where('role', 'superadmin')->latest()->get();

        $users = User::with('company')
            ->where('role', '!=', 'superadmin')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('email',      'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users', 'superadmins', 'search'));
    }

    public function storeSuperAdmin(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'superadmin',
            'is_active'  => true,
        ]);

        ActivityLog::record('user.superadmin_created', $user, $user->full_name . ' <' . $user->email . '>');

        return back()->with('success', "Super admin {$data['first_name']} {$data['last_name']} created.");
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLog::record('user.password_changed', $user, $user->full_name . ' <' . $user->email . '>', [], $user->company_id);

        return back()->with('success', "Password updated for {$user->full_name}.");
    }

    public function toggleActive(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate a super admin.');
        }
        $user->update(['is_active' => !$user->is_active]);

        ActivityLog::record(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            $user,
            $user->full_name . ' <' . $user->email . '>',
            [],
            $user->company_id
        );

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        ActivityLog::record('user.deleted', null, $user->full_name . ' <' . $user->email . '>', [], $user->company_id);
        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
