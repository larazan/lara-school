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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('type', 128)->comment('text,number,textarea,dropdown,checkbox,radio,fileupload');
            $table->boolean('is_required')->default(0);
            $table->text('default_values')->nullable()->comment('values of radio,checkbox,dropdown,etc');
            $table->text('other')->nullable()->comment('extra HTML attributes');
            // $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'school_id'], 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
