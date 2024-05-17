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
        Schema::create('online_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->string('question', 1024);
            $table->string('image_url', 1024)->nullable();
            $table->string('note', 1024)->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('last_edited_by')->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_exam_questions');
    }
};
