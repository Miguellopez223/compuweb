<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->foreignId('tienda_id')->after('id')->constrained('tiendas')->onDelete('cascade');
            $table->foreignId('producto_id')->after('tienda_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->after('producto_id')->constrained('users');
            $table->enum('tipo', ['entrada', 'salida'])->after('user_id');
            $table->integer('cantidad')->after('tipo');
            $table->decimal('precio_unitario', 10, 2)->nullable()->after('cantidad');
            $table->string('proveedor')->nullable()->after('precio_unitario');
            $table->string('referencia')->nullable()->after('proveedor');
        });
    }

    public function down(): void
    {
        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->dropForeign(['tienda_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['tienda_id', 'producto_id', 'user_id', 'tipo', 'cantidad', 'precio_unitario', 'proveedor', 'referencia']);
        });
    }
};
