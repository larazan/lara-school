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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('code', 64)->nullable();
            $table->string('bg_color', 32);
            $table->string('image', 512);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->string('type', 64)->comment('Theory / Practical');
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
        Schema::dropIfExists('subjects');
    }
};
