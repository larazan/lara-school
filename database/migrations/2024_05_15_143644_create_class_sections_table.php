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
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'section_id', 'medium_id'], 'unique_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
