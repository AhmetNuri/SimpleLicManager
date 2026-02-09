<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Insert default license types
        DB::table('license_types')->insert([
            ['code' => 'demo', 'name' => 'Demo', 'description' => 'Demo lisans', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'monthly', 'name' => 'Aylık', 'description' => '1 ay geçerli', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'yearly', 'name' => 'Yıllık', 'description' => '1 yıl geçerli', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'lifetime', 'name' => 'Ömür Boyu', 'description' => 'Süresiz lisans', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_types');
    }
};
