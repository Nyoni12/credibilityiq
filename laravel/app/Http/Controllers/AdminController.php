<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Assessment;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'companies'        => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'users'            => User::where('role', '!=', 'superadmin')->count(),
            'assessments'      => Assessment::count(),
            'open_assessments' => Assessment::where('status', 'open')->count(),
        ];

        $recentCompanies = Company::with('users', 'assessments')
            ->latest()->take(5)->get();

        return view('admin.index', compact('stats', 'recentCompanies'));
    }
}
