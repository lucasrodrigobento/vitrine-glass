<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->string('tipo', 20)->default('servico'); // servico | pagina
            $table->string('rota', 100)->nullable();
            $table->json('imagens')->nullable();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'ativo', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_features');
    }
};
