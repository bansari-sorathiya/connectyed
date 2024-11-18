<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchmaker_id')->constrained('users'); // Refers to the matchmaker (host)
            $table->string('zoom_meeting_id'); // The Zoom meeting ID
            $table->string('zoom_meeting_url'); // The Zoom meeting URL
            $table->dateTime('start_time'); // Meeting start time
            $table->integer('duration'); // Duration in minutes (15, 30, or 60)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
