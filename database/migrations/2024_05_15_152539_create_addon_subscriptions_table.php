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
        Schema::create('addon_subscriptions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->double('price', 8, 4);
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('status')->default(1)->comment('0 => Discontinue next billing, 1 => Continue');
            $table->unique(['feature_id', 'end_date'], 'addon_subscription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addon_subscriptions');
    }
};
