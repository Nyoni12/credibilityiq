<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyValue extends Model
{
    protected $fillable = [
        'company_id', 'name', 'description',
        'weight_percentage', 'order_position',
    ];

    protected $casts = [
        'weight_percentage' => 'float',
        'order_position'    => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function responseRatings()
    {
        return $this->hasMany(ResponseRating::class);
    }

    public function valueRatings()
    {
        return $this->hasMany(ValueRating::class);
    }
}
