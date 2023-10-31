<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->renameColumn('image_id', 'image_upload_id');
            $table->enum('status', ['Pending', 'Accepted', 'Cancelled'])->default('Pending')->change();
            $table->string('key')->index();

            $table->integer('reporter_id')->unsigned();
            $table->dropColumn('email');
            $table->string('reason', 500);

            $table->integer('staff_id')->unsigned()->nullable()->default(null);
            $table->text('staff_comments')->nullable()->default(null);
        });

        Schema::create('reporters', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('email')->nullable()->default(null);
            $table->string('ip');
            $table->boolean('is_banned')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->renameColumn('image_upload_id', 'image_id');
            $table->string('status')->default(null)->change();
            $table->dropColumn('key');

            $table->dropColumn('reporter_id');
            $table->string('email');
            $table->dropColumn('reason');

            $table->dropColumn('staff_id');
            $table->dropColumn('staff_comments');
        });

        Schema::dropIfExists('reporters');
    }
};
