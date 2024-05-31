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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount', 8, 2);
            $table->enum('payment_gateway', [1, 2])->comment('1 - razorpay 2 - stripe');
            $table->string('order_id')->comment('order_id / payment_intent_id');
            $table->string('payment_id')->nullable(true);
            $table->string('payment_signature')->nullable(true);
            $table->enum('payment_status', [0, 1, 2])->comment('0 - failed 1 - succeed 2 - pending');
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
        Schema::dropIfExists('payment_transactions');
    }
};
