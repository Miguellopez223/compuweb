<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('cliente_direccion');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_direccion')->nullable()->after('cliente_email');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('direccion')->nullable();
        });
    }
};
