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
            $table->string('instansi')->nullable();
            $table->string('version')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('dns_or_ip')->nullable();
            $table->text('description')->nullable();
            $table->string('server_location')->nullable();
            $table->string('risk_category')->nullable();
            $table->string('risk_category_doc')->nullable();
            $table->string('data_classification')->nullable();
            $table->string('data_classification_doc')->nullable();
            $table->string('personal_data_processed')->nullable();
            
            $table->boolean('req_security')->default(false);
            $table->boolean('req_protection')->default(false);
            $table->boolean('req_personal_data')->default(false);
            $table->boolean('req_feasibility')->default(false);
            $table->boolean('req_spbe')->default(false);
            
            $table->string('supporting_doc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pses', function (Blueprint $table) {
            $table->dropColumn([
                'instansi', 'version', 'contact_person', 'contact_phone', 
                'dns_or_ip', 'description', 'server_location', 
                'risk_category', 'risk_category_doc', 
                'data_classification', 'data_classification_doc', 
                'personal_data_processed',
                'req_security', 'req_protection', 'req_personal_data', 
                'req_feasibility', 'req_spbe', 'supporting_doc'
            ]);
        });
    }
};
