<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class AuthController extends Controller
{
    // ── Login / Logout ──────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = auth()->user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact your administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            ActivityLog::record('user.login', $user, $user->full_name . ' <' . $user->email . '>');

            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip'    => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        ActivityLog::record('user.logout', auth()->user(), auth()->user()?->full_name ?? '');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Registration ────────────────────────────────────────────────────────────

    public function showSignup()
    {
        if (!Setting::get('allow_self_registration', '1')) {
            return redirect()->route('login')->with('error', 'Self-registration is currently disabled. Please contact your administrator.');
        }
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        if (!Setting::get('allow_self_registration', '1')) {
            abort(403, 'Self-registration is currently disabled.');
        }

        $data = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users'],
            'company_name'   => ['required', 'string', 'max:200'],
            'industry'       => ['nullable', 'string', 'max:100'],
            'annual_revenue' => ['nullable', 'numeric', 'min:0'],
            'password'       => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $company = Company::create([
            'name'             => $data['company_name'],
            'industry'         => $data['industry'] ?? null,
            'annual_revenue'   => $data['annual_revenue'] ?? 0,
            'assessment_slots' => (int) Setting::get('default_assessment_slots', 1),
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'admin',
            'company_id' => $company->id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::record('company.self_registered', $company, $company->name, [
            'user_email' => $user->email,
        ], $company->id);

        return redirect()->route('dashboard')->with('success', 'Welcome to CredibilityIQ!');
    }

    // ── Profile ─────────────────────────────────────────────────────────────────

    public function profile()
    {
        return view('auth.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', "unique:users,email,{$user->id}"],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        ActivityLog::record('profile.updated', $user, $user->full_name);

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Forgot / Reset Password ──────────────────────────────────────────────────

    public function showForgotPassword(Request $request)
    {
        return view('auth.forgot-password', ['email' => $request->query('email', '')]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Always return success message to prevent email enumeration
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'If that email address is registered, we have sent a password reset link.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
                ActivityLog::record('user.password_reset', $user, $user->full_name . ' <' . $user->email . '>');
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password reset successfully. Please sign in with your new password.');
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
