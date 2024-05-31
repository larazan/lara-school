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
        Schema::create('optional_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->foreignId('fees_class_id')->nullable(true)->references('id')->on('fees_classes')->onDelete('cascade');
            $table->enum('mode', [1, 2, 3])->comment('1 - cash, 2 - cheque, 3 - online');
            $table->string('cheque_no')->nullable(true);
            $table->double('amount', 8, 2);
            $table->foreignId('fees_paid_id')->nullable(true)->references('id')->on('fees_paids')->onDelete('cascade');
            $table->date('date');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->enum('status', [1, 2])->comment('1 - succeed 2 - pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optional_fees');
    }
};
