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
        Schema::create('meeting_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings'); // References the meeting
            $table->foreignId('client_id')->constrained('users'); // References the client (user)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_client');
    }
};
