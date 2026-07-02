<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('cor_texto', 20)->nullable()->after('cor_secundaria');
            $table->string('cor_rodape_fundo', 20)->nullable()->after('cor_texto');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['cor_texto', 'cor_rodape_fundo']);
        });
    }
};
