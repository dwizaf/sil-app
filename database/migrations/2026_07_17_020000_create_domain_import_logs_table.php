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
        Schema::create('domain_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->comment('Nama file Excel yang diimpor');
            $table->foreignId('imported_by')->constrained('users')->comment('User yang melakukan import');
            $table->integer('total_records')->default(0)->comment('Total record di Excel');
            $table->integer('new_domains')->default(0)->comment('Domain baru yang ditambahkan');
            $table->integer('updated_domains')->default(0)->comment('Domain yang diperbarui');
            $table->integer('deactivated_domains')->default(0)->comment('Domain yang dinonaktifkan');
            $table->integer('reactivated_domains')->default(0)->comment('Domain yang diaktifkan kembali');
            $table->json('summary')->nullable()->comment('Detail ringkasan perubahan dalam format JSON');
            $table->timestamps();

            $table->index('imported_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_import_logs');
    }
};
