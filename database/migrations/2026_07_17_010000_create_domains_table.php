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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nama domain/subdomain');
            $table->string('type')->default('A')->comment('Tipe DNS record: A, CNAME, MX, dll');
            $table->string('content')->nullable()->comment('IP address atau target CNAME');
            $table->integer('ttl')->nullable()->comment('TTL value');
            $table->boolean('proxied')->default(false)->comment('Apakah melalui Cloudflare proxy');
            $table->string('category')->nullable()->comment('Kategori: pemerintah, docker, staging, production, lainnya');
            $table->string('status')->default('active')->comment('Status: active atau inactive');
            $table->dateTime('inactive_at')->nullable()->comment('Timestamp saat domain terdeteksi tidak aktif');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->dateTime('imported_at')->nullable()->comment('Kapan terakhir diimpor dari Cloudflare');
            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('inactive_at');
            $table->index('imported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
