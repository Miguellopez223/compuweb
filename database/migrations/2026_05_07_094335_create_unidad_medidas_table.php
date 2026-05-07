<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidad_medidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tienda_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 50);
            $table->string('abreviatura', 10);
            $table->timestamps();

            $table->unique(['tienda_id', 'nombre']);
        });

        // Seed default units for each existing store
        $tiendas = DB::table('tiendas')->pluck('id');
        $defaults = [
            ['nombre' => 'Unidad', 'abreviatura' => 'ud'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'kg'],
            ['nombre' => 'Litro', 'abreviatura' => 'lt'],
            ['nombre' => 'Metro', 'abreviatura' => 'm'],
        ];

        foreach ($tiendas as $tiendaId) {
            foreach ($defaults as $unit) {
                DB::table('unidad_medidas')->insert([
                    'tienda_id' => $tiendaId,
                    'nombre' => $unit['nombre'],
                    'abreviatura' => $unit['abreviatura'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unidad_medidas');
    }
};
