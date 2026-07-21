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
        Schema::table('pses', function (Blueprint $table) {
            $table->string('gdrive_folder_url')->nullable();
            $table->string('badge_link')->nullable();
            $table->string('badge_image')->nullable();
            $table->string('certificate_pdf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pses', function (Blueprint $table) {
            $table->dropColumn([
                'gdrive_folder_url', 'badge_link', 'badge_image', 'certificate_pdf'
            ]);
        });
    }
};
