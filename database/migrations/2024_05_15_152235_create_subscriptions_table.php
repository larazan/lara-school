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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->string('name');
            $table->double('student_charge', 8, 4);
            $table->double('staff_charge', 8, 4);
            $table->date('start_date');
            $table->date('end_date');
            $table->unique(['start_date'], 'subscription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
