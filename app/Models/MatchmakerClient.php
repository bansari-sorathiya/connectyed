<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchmakerClient extends Model
{
    use HasFactory;

    protected $table = 'matchmaker_clients';

    protected $fillable = [
        'matchmaker_id',
        'user_id',  // Added user_id
        'age',
        'height_feet',
        'height_inches',
        'body_type',
        'interests',
        'city',
        'state',
        'country',
        'marital_status',
        'children',
        'religion',
        'thumbnail_image',
        'additional_images'
    ];

    protected $casts = [
        'age' => 'integer',
        'height_feet' => 'integer',
        'height_inches' => 'integer',
        'children' => 'integer',
        'additional_images' => 'array'
    ];

    /**
     * Get the matchmaker that owns the client.
     */
    public function matchmaker()
    {
        return $this->belongsTo(User::class, 'matchmaker_id');
    }

    /**
     * Get the user associated with the client.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
