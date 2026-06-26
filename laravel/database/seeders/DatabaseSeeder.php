<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Company;
use App\Models\CompanyValue;
use App\Models\ResponseRating;
use App\Models\SurveyResponse;
use App\Models\User;
use App\Services\ScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // SuperAdmin
        User::firstOrCreate(['email' => 'admin@credibilityiq.com'], [
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'password'   => Hash::make('Admin@2025!'),
            'role'       => 'superadmin',
        ]);

        // Demo company
        $company = Company::firstOrCreate(['name' => 'Acme Corp'], [
            'industry'       => 'Technology',
            'annual_revenue' => 5_000_000,
        ]);

        // Demo admin user
        User::firstOrCreate(['email' => 'demo@acme.com'], [
            'first_name' => 'Demo',
            'last_name'  => 'Admin',
            'password'   => Hash::make('Demo@2025!'),
            'role'       => 'admin',
            'company_id' => $company->id,
        ]);

        // Company values
        $valuesData = [
            ['Integrity',       'Acting ethically in all dealings',         25],
            ['Innovation',      'Driving creative and modern solutions',    20],
            ['Accountability',  'Taking ownership of outcomes',             20],
            ['Customer Focus',  'Placing customers at the center',          20],
            ['Teamwork',        'Collaborating to achieve shared goals',    15],
        ];

        foreach ($valuesData as $i => [$name, $desc, $weight]) {
            CompanyValue::firstOrCreate(['company_id' => $company->id, 'name' => $name], [
                'description'      => $desc,
                'weight_percentage'=> $weight,
                'order_position'   => $i + 1,
            ]);
        }

        // Demo assessment with some responses
        $assessment = Assessment::firstOrCreate(
            ['company_id' => $company->id, 'title' => 'Q1 2025 Credibility Assessment'],
            ['status' => 'open']
        );

        if ($assessment->surveyResponses()->count() === 0) {
            $values = $company->values;
            // Add 12 sample responses
            $sampleScores = [
                [8,7,9,7,8],[7,8,7,8,9],[9,9,8,9,7],[6,7,8,7,8],[8,6,7,8,7],
                [9,8,9,8,9],[7,7,6,7,8],[8,9,8,8,7],[9,7,9,7,9],[6,8,7,8,6],
                [8,8,8,9,8],[7,9,8,7,9],
            ];

            foreach ($sampleScores as $scores) {
                $response = SurveyResponse::create([
                    'assessment_id'   => $assessment->id,
                    'respondent_role' => collect(['Customer / Client','Employee','Business Partner','Investor / Shareholder'])->random(),
                ]);
                foreach ($values as $idx => $value) {
                    ResponseRating::create([
                        'survey_response_id' => $response->id,
                        'company_value_id'   => $value->id,
                        'score'              => $scores[$idx] ?? 7,
                    ]);
                }
            }

            // Close and score the assessment
            (new ScoringService())->closeAssessment($assessment);
        }

        $this->command->info('Demo data seeded. Login: demo@acme.com / Demo@2025!');
        $this->command->info('SuperAdmin:   admin@credibilityiq.com / Admin@2025!');
    }
}
