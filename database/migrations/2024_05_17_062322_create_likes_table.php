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
        Schema::create('likes', function (Blueprint $table) {
            $table->morphs('userable');
            $table->morphs('likeable');
            $table->boolean('is_liked')->default(true)->index();
            $table->timestamps();
            $table->primary(array('userable_type', 'userable_id', 'likeable_type', 'likeable_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
