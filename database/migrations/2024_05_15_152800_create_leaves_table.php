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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('reason');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('status')->default(0)->comment('0 => Pending, 1 => Approved, 2 => Rejected');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
