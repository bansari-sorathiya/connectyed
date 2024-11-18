<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_all_day', // Ensure this field is fillable
    ];

    protected $casts = [
        'is_all_day' => 'boolean', // Cast to boolean
    ];

    /**
     * Get the user that owns the availability.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Assuming matchmakers are users
    }
}
