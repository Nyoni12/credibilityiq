<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_assessment_slots' => ['required', 'integer', 'min:1', 'max:50'],
            'support_email'            => ['required', 'email', 'max:200'],
            'allow_self_registration'  => ['boolean'],
            'platform_announcement'    => ['nullable', 'string', 'max:300'],
        ]);

        Setting::set('default_assessment_slots', (string) $data['default_assessment_slots']);
        Setting::set('support_email',            $data['support_email']);
        Setting::set('allow_self_registration',  $request->boolean('allow_self_registration') ? '1' : '0');
        Setting::set('platform_announcement',    $data['platform_announcement'] ?? '');

        ActivityLog::record('settings.updated', null, 'Platform Settings', [
            'default_slots'           => $data['default_assessment_slots'],
            'allow_self_registration' => $request->boolean('allow_self_registration'),
        ]);

        return back()->with('success', 'Platform settings saved.');
    }
}
