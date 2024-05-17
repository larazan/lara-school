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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('instructions', 1024)->nullable();
            $table->dateTime('due_date');
            $table->integer('points')->nullable();
            $table->boolean('resubmission')->default(0);
            $table->integer('extra_days_for_resubmission')->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('created_by')->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('edited_by')->nullable()->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
