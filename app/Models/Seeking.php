<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seeking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'min_age',
        'max_age',
        'location',
        'current_kids_number',
        'desired_kids_number',
        'gender',
        'bodytype',
        'haircolor',
        'maritalstatus',
        'religion',
        'smoker',
        'drinker',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'current_kids_number' => 'integer',
        'desired_kids_number' => 'integer',
        'smoker' => 'boolean',
    ];

    /**
     * Get the user that owns the seeking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
