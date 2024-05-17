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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->text('feedback')->nullable();
            $table->integer('points')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
