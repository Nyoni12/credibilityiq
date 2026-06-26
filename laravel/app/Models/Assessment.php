<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'company_id', 'title', 'status',
        'overall_score', 'total_leakage', 'closed_at',
    ];

    protected $casts = [
        'overall_score' => 'float',
        'total_leakage' => 'float',
        'closed_at'     => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function valueRatings()
    {
        return $this->hasMany(ValueRating::class);
    }

    public function getResponseCountAttribute(): int
    {
        return $this->surveyResponses()->count();
    }

    public function getScoreBadgeAttribute(): string
    {
        $score = $this->overall_score ?? 0;
        if ($score >= 80) return 'Excellent';
        if ($score >= 65) return 'Good';
        if ($score >= 50) return 'Fair';
        return 'Needs Work';
    }

    public function getScoreColorAttribute(): string
    {
        $score = $this->overall_score ?? 0;
        if ($score >= 80) return '#00A651';
        if ($score >= 65) return '#1F2192';
        if ($score >= 50) return '#f59e0b';
        return '#ef4444';
    }
}
