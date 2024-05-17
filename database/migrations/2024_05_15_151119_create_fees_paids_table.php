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
        Schema::create('fees_paids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->enum('is_fully_paid', [0, 1])->comment('0 - no 1 - yes');
            $table->double('amount', 8, 2);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->unique(['student_id', 'class_id', 'school_id', 'session_year_id'], 'unique_ids');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_paids');
    }
};
