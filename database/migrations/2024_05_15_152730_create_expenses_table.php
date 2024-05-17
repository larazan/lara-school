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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable(true)->references('id')->on('expense_categories')->onDelete('cascade');
            $table->string('ref_no')->nullable(true);
            $table->foreignId('staff_id')->nullable(true)->references('id')->on('staffs')->onDelete('cascade');
            $table->bigInteger('month')->nullable(true);
            $table->integer('year')->nullable(true);
            $table->string('title', 512);
            $table->string('description')->nullable(true);
            $table->double('amount', 8, 2);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['staff_id','month','year'],'salary_unique_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
