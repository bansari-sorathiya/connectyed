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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(null)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name',255);
            $table->unsignedBigInteger('matchmaker_id')->default(null)->nullable();
            $table->foreign('matchmaker_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('city',100);
            $table->string('state',100);
            $table->string('country',100);
            $table->string('location',100)->default(null)->nullable();
            $table->integer('age',11);
            $table->string('gender',25)->default(null)->nullable();
            $table->string('bodytype',50)->default(null)->nullable();
            $table->integer('height',11)->default(null)->nullable();
            $table->integer('inches',11)->default(null)->nullable();
            $table->string('haircolor',50)->default(null)->nullable();
            $table->string('maritalstatus',50)->default(null)->nullable();
            $table->integer('children',11)->default(null)->nullable();
            $table->string('religion',100)->default(null)->nullable();
            $table->tinyInteger('smoker',1)->default(null)->nullable();
            $table->string('drinker',20)->default(null)->nullable();
            $table->string('education',100)->default(null)->nullable();
            $table->integer('yearsexperience',11)->default(null)->nullable();
            $table->string('jobtitle',100)->default(null)->nullable();
            $table->text('sports')->default(null)->nullable();
            $table->text('hobbies')->default(null)->nullable();
            $table->text('languages')->default(null)->nullable();
            $table->string('english_level',50)->default(null)->nullable();
            $table->text('bio')->default(null)->nullable();
            $table->string('profile_image1',255)->default(null)->nullable();
            $table->string('profile_image2',255)->default(null)->nullable();
            $table->text('seeking')->default(null)->nullable();
            $table->string('occupation',150)->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
