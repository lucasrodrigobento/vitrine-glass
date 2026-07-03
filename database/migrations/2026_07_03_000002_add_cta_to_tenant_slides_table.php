<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_slides', function (Blueprint $table) {
            $table->string('titulo', 150)->nullable()->after('legenda');
            $table->string('subtitulo', 255)->nullable()->after('titulo');
            $table->string('botao_label', 100)->nullable()->after('subtitulo');
            $table->string('botao_url', 500)->nullable()->after('botao_label');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_slides', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'subtitulo', 'botao_label', 'botao_url']);
        });
    }
};
