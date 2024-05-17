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
        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreignId('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['subscription_id','feature_id'],'unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
