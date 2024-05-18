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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
			$table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('first_name');
            $table->string('mobile', 191)->after('phone')->default(null);
            $table->string('gender', 191)->after('password')->default(null);
            $table->string('image', 512)->after('gender')->default(null);
            $table->date('dob')->after('image')->default(null);
            $table->string('current_address', 191)->after('dob')->default(null);
            $table->string('permanent_address', 191)->after('current_address')->default(null);
            $table->string('occupation')->after('permanent_address')->default(null);
            $table->tinyInteger('status')->after('occupation')->default(1);
            $table->tinyInteger('reset_request')->after('status')->default(0);
            $table->string('fcm_id', 1024)->after('status')->default(null);
            $table->bigInteger('school_id', 20)->after('fcm_id')->default(null);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
