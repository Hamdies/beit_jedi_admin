<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per signed-in owner-app device.
 *
 * `vendors.auth_token` / `vendors.firebase_token` hold a single value each, so a
 * second phone signing in overwrote the first one's token and logged it out, and
 * only the last phone to register received order pushes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->index();
            $table->string('auth_token', 191)->unique();
            $table->string('fcm_token')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_devices');
    }
};
