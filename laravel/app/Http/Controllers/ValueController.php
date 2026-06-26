<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyValue;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $values  = $company->values()->get();

        return view('setup.values', compact('company', 'values'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string'],
            'weight_percentage'=> ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $maxOrder = $company->values()->max('order_position') ?? 0;
        $company->values()->create(array_merge($data, ['order_position' => $maxOrder + 1]));

        return back()->with('success', 'Value added.');
    }

    public function update(Request $request, CompanyValue $value)
    {
        $this->authorizeValue($value);

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string'],
            'weight_percentage'=> ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $value->update($data);
        return back()->with('success', 'Value updated.');
    }

    public function destroy(CompanyValue $value)
    {
        $this->authorizeValue($value);
        $value->delete();
        return back()->with('success', 'Value removed.');
    }

    public function reorder(Request $request)
    {
        $company = auth()->user()->company;
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $position => $id) {
            CompanyValue::where('id', $id)
                ->where('company_id', $company->id)
                ->update(['order_position' => $position + 1]);
        }

        return response()->json(['ok' => true]);
    }

    private function authorizeValue(CompanyValue $value): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $value->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
