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
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->string('type', 32)->comment('Compulsory / Elective');
            $table->foreignId('elective_subject_group_id')->nullable()->comment('if type=Elective')->references('id')->on('elective_subject_groups')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('cascade');
            $table->integer('virtual_semester_id')->virtualAs('CASE WHEN semester_id IS NOT NULL THEN semester_id ELSE 0 END');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'subject_id', 'virtual_semester_id'], 'unique_ids');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
