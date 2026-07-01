<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('nome', 150);
            $table->string('dominio', 150)->unique();
            $table->boolean('ativo')->default(true);

            // Identidade visual
            $table->string('cor_primaria', 20)->default('#ed3237');
            $table->string('cor_secundaria', 20)->default('#9e2016');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('og_image')->nullable();

            // Contato
            $table->string('whatsapp', 20);
            $table->string('whatsapp_exibir', 30);
            $table->string('email', 150);
            $table->string('instagram', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->json('areas_atendidas')->nullable();

            // Integrações
            $table->string('google_ads_id', 50)->nullable();
            $table->text('google_maps_embed')->nullable();
            $table->string('schema_type', 50)->default('LocalBusiness');

            // SEO
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('seo_keywords', 500)->nullable();

            // Página Home
            $table->string('hero_titulo', 255)->nullable();
            $table->string('hero_subtitulo', 255)->nullable();

            // Página Sobre
            $table->string('sobre_titulo', 150)->nullable();
            $table->text('sobre_descricao')->nullable();
            $table->text('sobre_missao')->nullable();
            $table->text('sobre_visao')->nullable();
            $table->text('sobre_valores')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
