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
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->string('akedly_transaction_req_id')->nullable()->index();
            $table->string('akedly_main_transaction_id')->nullable();
            $table->string('token')->nullable()->change();
        });

        Schema::table('password_resets', function (Blueprint $table) {
            $table->string('akedly_transaction_req_id')->nullable()->index();
            $table->string('akedly_main_transaction_id')->nullable();
            $table->string('token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->dropColumn(['akedly_transaction_req_id', 'akedly_main_transaction_id']);
        });

        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn(['akedly_transaction_req_id', 'akedly_main_transaction_id']);
        });
    }
};
