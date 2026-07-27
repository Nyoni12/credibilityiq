<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValueRating extends Model
{
    protected $fillable = [
        'assessment_id', 'company_value_id',
        'avg_score', 'financial_impact', 'training_recommended',
    ];

    protected $casts = [
        'avg_score'            => 'float',
        'financial_impact'     => 'float',
        'training_recommended' => 'boolean',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function companyValue()
    {
        return $this->belongsTo(CompanyValue::class);
    }

    public function getScorePercentAttribute(): float
    {
        return round(($this->avg_score - 1) / 9 * 100, 1);
    }
}
