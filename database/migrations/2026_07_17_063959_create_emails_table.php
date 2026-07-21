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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('email_address')->unique();
            $table->string('opd')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->integer('quota_mb')->nullable();
            $table->string('status')->default('active'); // active, inactive, suspend
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
