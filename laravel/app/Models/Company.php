<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'name', 'logo_path', 'industry', 'domain',
        'subscription_tier', 'survey_token',
        'annual_revenue', 'is_active', 'assessment_slots',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'annual_revenue'   => 'float',
        'assessment_slots' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($company) {
            if (empty($company->survey_token)) {
                $company->survey_token = Str::random(32);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function values()
    {
        return $this->hasMany(CompanyValue::class)->orderBy('order_position');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class)->latest();
    }

    public function latestAssessment()
    {
        return $this->hasOne(Assessment::class)->latestOfMany();
    }

    public function canCreateAssessment(): bool
    {
        return $this->assessments()->count() < ($this->assessment_slots ?? 1);
    }

    public function regenerateSurveyToken(): string
    {
        $this->update(['survey_token' => Str::random(32)]);
        return $this->survey_token;
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && file_exists(public_path('storage/' . $this->logo_path))) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-logo.png');
    }
}
