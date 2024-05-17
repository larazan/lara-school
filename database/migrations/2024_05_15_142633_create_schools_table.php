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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('support_phone');
            $table->string('support_email');
            $table->string('tagline');
            $table->string('logo');
            $table->foreignId('admin_id')->nullable()->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(0)->comment('0 => Deactivate, 1 => Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
