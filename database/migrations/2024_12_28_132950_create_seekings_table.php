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
        Schema::create('seekings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('min_age');
            $table->integer('max_age');
            $table->string('location',255);
            $table->integer('current_kids_number');
            $table->integer('desired_kids_number');
            $table->string('gender',255);
            $table->string('bodytype',255);
            $table->string('haircolor',255);
            $table->string('maritalstatus',255);
            $table->string('religion',255);
            $table->tinyInteger('smoker');
            $table->string('drinker',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seekings');
    }
};
