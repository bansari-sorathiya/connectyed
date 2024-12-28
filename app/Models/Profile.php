<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'matchmaker_id',
        'city',
        'state',
        'country',
        'location',
        'age',
        'gender',
        'bodytype',
        'english_level', // Added this field
        'height',
        'inches',
        'haircolor',
        'maritalstatus',
        'children',
        'religion',
        'smoker',
        'drinker',
        'education',
        'jobtitle',
        'sports',
        'hobbies',
        'languages',
        'bio',
        'profile_image1',
        'profile_image2',
        'yearsexperience',
        'name', 
        'seeking',
        'occupation',
        // 'min_age',
        // 'max_age',
        // 'desired_children',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'yearsexperience' => 'integer',
        'children' => 'integer',
        'smoker' => 'boolean',
        'height' => 'integer',
        'inches' => 'integer',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
