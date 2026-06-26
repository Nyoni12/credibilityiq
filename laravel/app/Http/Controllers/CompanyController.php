<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->search;
        $companies = Company::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount(['users', 'assessments'])
            ->with('latestAssessment')
            ->latest()
            ->paginate(15);

        return view('admin.companies.index', compact('companies', 'search'));
    }

    public function show(Company $company)
    {
        $company->load(['users', 'values', 'assessments']);
        return view('admin.companies.show', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:200'],
            'industry'       => ['nullable', 'string', 'max:100'],
            'domain'         => ['nullable', 'url', 'max:200'],
            'subscription_tier' => ['required', 'in:basic,standard,premium'],
            'annual_revenue' => ['nullable', 'numeric', 'min:0'],
            'is_active'      => ['boolean'],
            'logo'           => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);
        $company->update($data);

        return back()->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted.');
    }

    public function regenerateToken(Company $company)
    {
        $token = $company->regenerateSurveyToken();
        return back()->with('success', "New survey token generated: {$token}");
    }

    // User management within a company
    public function storeUser(Request $request, Company $company)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users'],
            'role'       => ['required', 'in:admin,viewer'],
            'password'   => ['required', 'min:8'],
        ]);

        $company->users()->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'password'   => Hash::make($data['password']),
        ]);

        return back()->with('success', 'User added to company.');
    }
}
