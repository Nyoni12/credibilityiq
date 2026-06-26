<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseRating extends Model
{
    protected $fillable = ['survey_response_id', 'company_value_id', 'score'];

    protected $casts = ['score' => 'integer'];

    public function surveyResponse()
    {
        return $this->belongsTo(SurveyResponse::class);
    }

    public function companyValue()
    {
        return $this->belongsTo(CompanyValue::class);
    }
}
