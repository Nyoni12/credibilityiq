<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Setting;
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
            'name'              => ['required', 'string', 'max:200'],
            'industry'          => ['nullable', 'string', 'max:100'],
            'domain'            => ['nullable', 'url', 'max:200'],
            'subscription_tier' => ['required', 'in:basic,standard,premium'],
            'annual_revenue'    => ['nullable', 'numeric', 'min:0'],
            'is_active'         => ['boolean'],
            'logo'              => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);
        $company->update($data);

        ActivityLog::record('company.updated', $company, $company->name, [
            'changes' => array_keys($request->except(['_token', '_method', 'logo'])),
        ], $company->id);

        return back()->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        ActivityLog::record('company.deleted', null, $company->name . ' (id:' . $company->id . ')');
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted.');
    }

    public function regenerateToken(Company $company)
    {
        $token = $company->regenerateSurveyToken();
        ActivityLog::record('company.token_regenerated', $company, $company->name, [], $company->id);
        return back()->with('success', "New survey token generated: {$token}");
    }

    public function onboard(Request $request)
    {
        $data = $request->validate([
            'company_name'      => ['required', 'string', 'max:200'],
            'industry'          => ['nullable', 'string', 'max:100'],
            'annual_revenue'    => ['nullable', 'numeric', 'min:0'],
            'subscription_tier' => ['required', 'in:basic,standard,premium'],
            'first_name'        => ['required', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'email'             => ['required', 'email', 'unique:users'],
            'password'          => ['required', 'min:8', 'confirmed'],
        ]);

        $company = Company::create([
            'name'              => $data['company_name'],
            'industry'          => $data['industry'] ?? null,
            'annual_revenue'    => $data['annual_revenue'] ?? 0,
            'subscription_tier' => $data['subscription_tier'],
            'is_active'         => true,
            'assessment_slots'  => (int) Setting::get('default_assessment_slots', 1),
        ]);

        $company->users()->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        ActivityLog::record('company.created', $company, $company->name, [
            'admin_email' => $data['email'],
            'tier'        => $data['subscription_tier'],
        ], $company->id);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', "Company \"{$company->name}\" onboarded. Admin login: {$data['email']}");
    }

    public function grantAssessmentSlot(Company $company)
    {
        $company->increment('assessment_slots');
        $used = $company->assessments()->count();

        ActivityLog::record('company.slot_granted', $company, $company->name, [
            'new_slots' => $company->assessment_slots,
            'used'      => $used,
        ], $company->id);

        return back()->with('success', "{$company->name} can now create assessment #" . $company->assessment_slots . " (currently used: {$used}).");
    }

    public function storeUser(Request $request, Company $company)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users'],
            'role'       => ['required', 'in:admin,viewer'],
            'password'   => ['required', 'min:8'],
        ]);

        $user = $company->users()->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'password'   => Hash::make($data['password']),
        ]);

        ActivityLog::record('company.user_added', $user, $data['first_name'] . ' ' . $data['last_name'] . ' <' . $data['email'] . '>', [
            'role' => $data['role'],
        ], $company->id);

        return back()->with('success', 'User added to company.');
    }
}
