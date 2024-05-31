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
        Schema::create('online_exam_student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('online_exam_question_choices')->onDelete('cascade');
            $table->foreignId('option_id')->references('id')->on('online_exam_question_options')->onDelete('cascade');
            $table->date('submitted_date');
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
        Schema::dropIfExists('online_exam_student_answers');
    }
};
