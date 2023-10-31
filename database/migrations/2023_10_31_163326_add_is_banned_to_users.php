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
            //
            $table->boolean('is_banned')->default(0);
            $table->text('ban_reason')->nullable()->default(null);
            $table->dateTime('banned_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('is_banned');
            $table->dropColumn('ban_reason');
            $table->dropColumn('banned_at');
        });
    }
};
