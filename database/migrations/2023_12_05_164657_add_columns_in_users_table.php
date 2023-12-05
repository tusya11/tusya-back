<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->references('id')->on('roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('profile_id')->nullable()->references('id')->on('profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');
            $table->string('name');
        });
    }
};