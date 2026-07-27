<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CompanyValue;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    const CFA_VALUES = [
        ['name' => 'Teamwork',                    'hint' => 'Collaborative effort toward shared goals'],
        ['name' => 'Integrity',                   'hint' => 'Adherence to ethical principles and moral standards'],
        ['name' => 'Reputation',                  'hint' => 'How the organisation is perceived externally'],
        ['name' => 'Respect for self and others', 'hint' => 'Dignified treatment at all organisational levels'],
        ['name' => 'Humility',                    'hint' => 'Openness to feedback and continuous learning'],
        ['name' => 'Empathy',                     'hint' => "Understanding others' perspectives and emotional needs"],
        ['name' => 'Fairness',                    'hint' => 'Just and impartial treatment of all stakeholders'],
        ['name' => 'Hardwork',                    'hint' => 'Consistent diligence and effort in all tasks'],
        ['name' => 'Responsibility',              'hint' => 'Ownership of actions and their outcomes'],
        ['name' => 'Accountability',              'hint' => 'Answering for results and commitments made'],
        ['name' => 'Trustworthiness',             'hint' => 'Reliability and dependability in all dealings'],
        ['name' => 'Honesty without offense',     'hint' => 'Truthful communication delivered with respect and care'],
    ];

    public function index()
    {
        $company    = auth()->user()->company;
        $existing   = $company->values()->get()->keyBy('name');
        $cfaValues  = self::CFA_VALUES;

        return view('setup.values', compact('company', 'existing', 'cfaValues'));
    }

    public function bulkSave(Request $request)
    {
        $company = auth()->user()->company;

        $request->validate(['values' => 'array|max:12']);

        $toSave = collect($request->input('values', []))
            ->filter(fn ($v) => ($v['selected'] ?? '0') === '1');

        $selectedNames = $toSave->pluck('name')->toArray();

        $company->values()
            ->whereNotIn('name', $selectedNames)
            ->whereDoesntHave('responseRatings')
            ->delete();

        $company->values()
            ->whereNotIn('name', $selectedNames)
            ->whereHas('responseRatings')
            ->update(['financial_weight' => 0, 'weight_percentage' => 0]);

        foreach ($toSave->values() as $idx => $item) {
            $company->values()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'description'       => $item['description'] ?? null,
                    'financial_weight'  => max(0, (float) ($item['financial_weight'] ?? 0)),
                    'weight_percentage' => 0,
                    'order_position'    => $idx + 1,
                ]
            );
        }

        ActivityLog::record('values.updated', null, $toSave->count() . ' values saved', [
            'selected' => $selectedNames,
        ], $company->id);

        return back()->with('success', $toSave->count() . ' values saved successfully.');
    }

    public function destroy(CompanyValue $value)
    {
        $this->authorizeValue($value);
        ActivityLog::record('value.deleted', $value, $value->name, [], $value->company_id);
        $value->delete();
        return back()->with('success', 'Value removed.');
    }

    private function authorizeValue(CompanyValue $value): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $value->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
