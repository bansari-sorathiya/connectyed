<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'matchmaker_id',
        'client_id',
        'google_meet_password',
        'google_meet_id',
        'google_meet_link',
        'meeting_response',
        'start_time',
        'duration',
        'status',
        'scheduled_by', // Ensure this field exists in the migration
    ];

    /**
     * A meeting belongs to many clients (users).
     */
    public function clients()
    {
        return $this->belongsToMany(User::class, 'meeting_client', 'meeting_id', 'client_id');
    }

    /**
     * A meeting belongs to a matchmaker (host).
     */
    public function matchmaker()
    {
        return $this->belongsTo(User::class, 'matchmaker_id');
    }

    /**
     * A meeting is scheduled by a user.
     */
    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}
