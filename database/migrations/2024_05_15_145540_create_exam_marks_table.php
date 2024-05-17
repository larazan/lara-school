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
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_timetable_id')->references('id')->on('exam_timetables')->onDelete('cascade');
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->float('obtained_marks');
            $table->string('teacher_review', 1024)->nullable()->default(NULL);
            $table->boolean('passing_status')->comment('1=Pass, 0=Fail');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyText('grade')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
