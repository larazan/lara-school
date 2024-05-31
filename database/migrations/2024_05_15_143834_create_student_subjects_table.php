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
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};
