<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('unidad_medida', 20)->default('unidad')->after('stock_minimo');
        });

        Schema::create('atributo_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('nombre');
            $table->string('valor');
            $table->timestamps();
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropForeign(['especialista_id']);
            $table->dropColumn('especialista_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('visible_catalogo')->default(true)->after('whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('visible_catalogo');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->foreignId('especialista_id')->nullable()->constrained('users');
        });

        Schema::dropIfExists('atributo_productos');

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('unidad_medida');
        });
    }
};
