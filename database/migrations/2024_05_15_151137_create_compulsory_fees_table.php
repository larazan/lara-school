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
        Schema::create('compulsory_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->enum('type', [1, 2])->comment('1 - Full Payment , 2 - Installment Payment');
            $table->foreignId('installment_id')->nullable(true)->references('id')->on('installment_fees')->onDelete('cascade');
            $table->enum('mode', [1, 2, 3])->comment('1 - cash, 2 - cheque, 3 - online');
            $table->string('cheque_no')->nullable(true);
            $table->double('amount', 8, 2);
            $table->double('due_charges', 8, 2)->nullable(true);
            $table->foreignId('fees_paid_id')->nullable(true)->references('id')->on('fees_paids')->onDelete('cascade');
            $table->enum('status', [1, 2])->comment('1 - succeed 2 - pending');
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compulsory_fees');
    }
};
