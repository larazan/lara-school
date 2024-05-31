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
        Schema::create('promote_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyInteger('result')->default(1)->comment('1=>Pass,0=>fail');
            $table->tinyInteger('status')->default(1)->comment('1=>continue,0=>leave');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['student_id', 'class_section_id', 'session_year_id'], 'unique_columns');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promote_students');
    }
};
