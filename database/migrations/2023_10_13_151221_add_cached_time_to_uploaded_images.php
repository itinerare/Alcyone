<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('image_uploads', function (Blueprint $table) {
            //
            $table->dateTime('cache_expiry')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('image_uploads', function (Blueprint $table) {
            //
            $table->dropColumn('cache_expiry');
        });
    }
};
