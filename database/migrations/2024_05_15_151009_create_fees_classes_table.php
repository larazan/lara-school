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
        Schema::create('fees_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('fees_type_id')->references('id')->on('fees_types')->onDelete('cascade');
            $table->float('amount');
            $table->enum('choiceable', [0, 1])->comment('0 - no, 1 - yes')->default(0);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'fees_type_id', 'school_id'], 'unique_ids');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees_classes');
    }
};
