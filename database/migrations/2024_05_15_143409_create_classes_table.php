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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('include_semesters')->comment('0 - no 1 - yes')->default(0);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->references('id')->on('shifts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stream_id')->nullable()->references('id')->on('streams')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('classes');
    }
};
