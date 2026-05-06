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
        Schema::table('tiendas', function (Blueprint $table) {
            $table->string('direccion')->nullable()->after('telefono_principal');
            $table->string('logo')->nullable()->after('direccion');
            $table->string('nit')->nullable()->after('logo');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_email')->nullable()->after('cliente_telefono');
            $table->string('cliente_direccion')->nullable()->after('cliente_email');
            $table->string('cliente_nit')->nullable()->after('cliente_direccion');
            $table->string('codigo_pedido')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'logo', 'nit']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['cliente_email', 'cliente_direccion', 'cliente_nit', 'codigo_pedido']);
        });
    }
};
