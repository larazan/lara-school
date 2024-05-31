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
        Schema::create('class_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('teacher_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_section_id', 'teacher_id'], 'unique_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_teachers');
    }
};
