<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function tienda(string $slug): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        return response()->json([
            'id'                 => $tienda->id,
            'nombre'             => $tienda->nombre,
            'slug'               => $tienda->slug,
            'telefono_principal' => $tienda->telefono_principal,
            'direccion'          => $tienda->direccion,
            'logo'               => $tienda->logo,
            'nit'                => $tienda->nit,
        ]);
    }

    public function categorias(string $slug): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        $categorias = Categoria::withoutGlobalScopes()
            ->where('tienda_id', $tienda->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($categorias);
    }

    public function productos(Request $request, string $slug): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        $query = Producto::withoutGlobalScopes()
            ->with(['categoria:id,nombre', 'atributos'])
            ->where('tienda_id', $tienda->id)
            ->where('estado', 'Disponible')
            ->where('stock', '>', 0);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($qb) =>
                $qb->where('nombre', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
            );
        }

        $perPage = min((int) $request->get('per_page', 12), 50);

        match ($request->get('sort', 'nombre')) {
            'precio_asc'  => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            default       => $query->orderBy('nombre', 'asc'),
        };

        $productos = $query->paginate($perPage);

        $productos->getCollection()->transform(fn($p) => [
            'id'            => $p->id,
            'nombre'        => $p->nombre,
            'sku'           => $p->sku,
            'descripcion'   => $p->descripcion,
            'precio'        => $p->precio,
            'stock'         => $p->stock,
            'unidad_medida' => $p->unidad_medida,
            'imagen_url'    => $p->imagen
                ? (str_starts_with($p->imagen, 'http')
                    ? $p->imagen
                    : rtrim(config('app.url'), '/') . '/storage/' . $p->imagen)
                : null,
            'categoria'     => $p->categoria ? ['id' => $p->categoria->id, 'nombre' => $p->categoria->nombre] : null,
            'atributos'     => $p->atributos->map(fn($a) => ['nombre' => $a->nombre, 'valor' => $a->valor]),
        ]);

        return response()->json($productos);
    }

    public function producto(string $slug, int $id): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        $producto = Producto::withoutGlobalScopes()
            ->with(['categoria:id,nombre', 'atributos'])
            ->where('tienda_id', $tienda->id)
            ->where('estado', 'Disponible')
            ->findOrFail($id);

        return response()->json([
            'id'            => $producto->id,
            'nombre'        => $producto->nombre,
            'sku'           => $producto->sku,
            'descripcion'   => $producto->descripcion,
            'precio'        => $producto->precio,
            'stock'         => $producto->stock,
            'unidad_medida' => $producto->unidad_medida,
            'imagen_url'    => $producto->imagen
                ? (str_starts_with($producto->imagen, 'http')
                    ? $producto->imagen
                    : rtrim(config('app.url'), '/') . '/storage/' . $producto->imagen)
                : null,
            'categoria'     => $producto->categoria ? ['id' => $producto->categoria->id, 'nombre' => $producto->categoria->nombre] : null,
            'atributos'     => $producto->atributos->map(fn($a) => ['nombre' => $a->nombre, 'valor' => $a->valor]),
        ]);
    }

    public function vendedores(string $slug): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        $vendedores = User::where('tienda_id', $tienda->id)
            ->where('visible_catalogo', true)
            ->whereNotNull('whatsapp_number')
            ->get(['id', 'name', 'role', 'whatsapp_number']);

        return response()->json($vendedores);
    }
}
