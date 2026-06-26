<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = ['assessment_id', 'respondent_role'];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function ratings()
    {
        return $this->hasMany(ResponseRating::class);
    }
}
