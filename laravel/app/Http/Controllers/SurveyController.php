<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Assessment;
use App\Models\SurveyResponse;
use App\Models\ResponseRating;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function show(Request $request, string $token)
    {
        $company = Company::where('survey_token', $token)->firstOrFail();

        $assessment = $company->assessments()
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$assessment) {
            return view('survey.closed', compact('company'));
        }

        // Block repeat submissions from the same IP for this assessment
        if (SurveyResponse::where('assessment_id', $assessment->id)
                ->where('ip_address', $request->ip())
                ->exists()) {
            return view('survey.already_submitted', compact('company'));
        }

        $values = $company->values()->get();

        if ($values->isEmpty()) {
            return view('survey.no_values', compact('company'));
        }

        return view('survey.show', compact('company', 'assessment', 'values', 'token'));
    }

    public function submit(Request $request, string $token)
    {
        $company = Company::where('survey_token', $token)->firstOrFail();

        $assessment = $company->assessments()
            ->where('status', 'open')
            ->latest()
            ->firstOrFail();

        // Double-check IP deduplication on submit (guards against race condition on show)
        if (SurveyResponse::where('assessment_id', $assessment->id)
                ->where('ip_address', $request->ip())
                ->exists()) {
            return view('survey.already_submitted', compact('company'));
        }

        $values = $company->values()->pluck('id')->toArray();

        $rules = [];
        foreach ($values as $id) {
            $rules["scores.{$id}"] = ['required', 'integer', 'min:1', 'max:10'];
        }
        $rules['respondent_role'] = ['nullable', 'string', 'max:100'];

        $data = $request->validate($rules);

        $response = SurveyResponse::create([
            'assessment_id'   => $assessment->id,
            'respondent_role' => $data['respondent_role'] ?? null,
            'ip_address'      => $request->ip(),
        ]);

        foreach ($data['scores'] as $valueId => $score) {
            ResponseRating::create([
                'survey_response_id' => $response->id,
                'company_value_id'   => $valueId,
                'score'              => (int) $score,
            ]);
        }

        return view('survey.thank_you', compact('company'));
    }
}
