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
        Schema::create('subscription_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->string('description')->nullable(true);
            $table->double('amount', 8, 4);
            $table->bigInteger('total_student');
            $table->bigInteger('total_staff');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->date('due_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['subscription_id', 'school_id'], 'subscription_bill');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_bills');
    }
};
