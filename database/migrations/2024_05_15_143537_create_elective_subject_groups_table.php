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
        Schema::create('elective_subject_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('total_subjects');
            $table->integer('total_selectable_subjects');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('cascade');
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
        Schema::dropIfExists('elective_subject_groups');
    }
};
