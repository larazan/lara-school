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
        Schema::create('online_exam_question_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('online_exam_questions')->onDelete('cascade');
            $table->integer('marks')->nullable();
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
        Schema::dropIfExists('online_exam_question_choices');
    }
};
