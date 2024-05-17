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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('description')->nullable(true);
            $table->string('tagline')->nullable(true);
            $table->float('student_charge', 8, 4)->default(0);
            $table->float('staff_charge', 8, 4)->default(0);
            $table->tinyInteger('status')->default(0)->comment('0 => Unpublished, 1 => Published');
            $table->tinyInteger('highlight')->default(0)->comment('0 => No, 1 => Yes');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
