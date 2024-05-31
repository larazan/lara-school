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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('total_marks');
            $table->float('obtained_marks');
            $table->float('percentage');
            $table->tinyText('grade');

            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
