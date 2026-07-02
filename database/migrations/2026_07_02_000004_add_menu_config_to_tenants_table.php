<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('menu_servicos_label', 50)->default('Serviços')->after('sobre_valores');
            $table->boolean('menu_catalogo_visivel')->default(true)->after('menu_servicos_label');
            $table->string('menu_catalogo_label', 50)->default('Catálogo de Serviços')->after('menu_catalogo_visivel');
            // 'dropdown' = dentro do menu Serviços | 'topo' = item separado no menu principal
            $table->string('menu_catalogo_posicao', 10)->default('dropdown')->after('menu_catalogo_label');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'menu_servicos_label',
                'menu_catalogo_visivel',
                'menu_catalogo_label',
                'menu_catalogo_posicao',
            ]);
        });
    }
};
