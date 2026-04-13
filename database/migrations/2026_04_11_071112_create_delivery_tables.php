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
    
    // 1. Customer Profiles
    Schema::create('customer_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('first_name');
        $table->string('last_name');
        $table->string('avatar')->nullable();
        $table->integer('loyalty_pts')->default(0);
        $table->timestamps();
    });

    // 2. Hotel Profiles
    Schema::create('hotel_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('hotel_name');
        $table->text('description')->nullable();
        $table->string('address');
        $table->decimal('lat', 10, 8)->nullable();
        $table->decimal('long', 11, 8)->nullable();
        $table->time('opens_at')->default('08:00:00');
        $table->time('closes_at')->default('22:00:00');
        $table->boolean('is_available')->default(true);
        $table->timestamps();
    });

    // 3. Admin Profiles
    Schema::create('admin_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('employee_id')->nullable();
        $table->string('department')->default('General');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_tables');
    }
};
