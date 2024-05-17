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
        Schema::create('session_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('default')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['name', 'school_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_years');
    }
};
