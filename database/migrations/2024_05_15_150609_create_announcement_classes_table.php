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
        Schema::create('announcement_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->nullable()->unsigned()->index()->references('id')->on('announcements')->onDelete('cascade');
            $table->foreignId('class_section_id')->nullable()->unsigned()->index()->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->nullable(true)->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['announcement_id', 'class_section_id', 'school_id'], 'unique_columns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_classes');
    }
};
