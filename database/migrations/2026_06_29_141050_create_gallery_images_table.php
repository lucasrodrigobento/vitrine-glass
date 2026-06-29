<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_slug', 50);
            $table->string('categoria', 50);
            $table->string('path');
            $table->string('titulo', 150)->nullable();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['tenant_slug', 'categoria', 'ativo', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
