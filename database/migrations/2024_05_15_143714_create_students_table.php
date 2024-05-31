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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->string('admission_no', 512);
            $table->integer('roll_number')->nullable();
            $table->date('admission_date');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('guardian_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
