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
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('hotel_id')
    ->constrained('hotel_profiles')
    ->onDelete('cascade');
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->string('category');

    $table->integer('preparation_time')->default(10);

    $table->boolean('is_available')->default(true);
    $table->boolean('is_featured')->default(false);

    $table->text('ingredients')->nullable();
    $table->integer('calories')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
