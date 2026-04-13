<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // We add the fields missing from the default Laravel table
        $table->string('phone')->unique()->after('password');
        $table->enum('role', ['customer', 'hotel', 'admin'])->default('customer')->after('phone');
        $table->boolean('is_active')->default(true)->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'role', 'is_active']);
    });
}
};
