<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            $table->string('nombre');
            $table->string('ci_nit')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->index(['tienda_id', 'nombre']);
            $table->index(['tienda_id', 'ci_nit']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->after('tienda_id')->constrained('clientes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
        });

        Schema::dropIfExists('clientes');
    }
};
