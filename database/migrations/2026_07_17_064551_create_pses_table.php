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
        Schema::create('pses', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->string('url')->nullable();
            $table->string('opd')->nullable();
            $table->string('tdpse_number')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('sector')->default('publik');
            $table->string('status')->default('belum_terdaftar'); // belum_terdaftar, proses, terdaftar
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pses');
    }
};
