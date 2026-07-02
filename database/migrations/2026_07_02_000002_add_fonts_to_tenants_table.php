<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('font_body', 100)->default('Open Sans')->after('cor_secundaria');
            $table->string('font_heading', 100)->default('Righteous')->after('font_body');
            $table->string('font_accent', 100)->default('Josefin Sans')->after('font_heading');
            $table->text('font_google_url')->nullable()->after('font_accent');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['font_body', 'font_heading', 'font_accent', 'font_google_url']);
        });
    }
};
