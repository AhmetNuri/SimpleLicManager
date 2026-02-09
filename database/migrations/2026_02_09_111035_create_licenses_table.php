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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_id', 255)->nullable();
            $table->string('serial_number', 64)->unique();
            $table->timestamp('starts_at');
            $table->timestamp('last_checked_date')->nullable();
            $table->string('last_checked_device_id', 255)->nullable();
            $table->boolean('emergency')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->enum('license_type', ['demo', 'monthly', 'yearly', 'lifetime'])->default('monthly');
            $table->string('product_package', 255);
            $table->boolean('user_enable')->default(true);
            $table->unsignedInteger('max_connection_count')->default(1);
            $table->timestamps();

            $table->index('user_id');
            $table->index('serial_number');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
