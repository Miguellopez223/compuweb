<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_costo', 10, 2)->default(0)->after('precio');
        });

        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->string('motivo')->nullable()->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('precio_costo');
        });

        Schema::table('movimiento_inventarios', function (Blueprint $table) {
            $table->dropColumn('motivo');
        });
    }
};
