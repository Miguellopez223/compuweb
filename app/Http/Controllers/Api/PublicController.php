<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Catálogo Público
 */
class PublicController extends Controller
{
    /**
     * Información de la tienda
     *
     * Retorna los datos públicos de una tienda a partir de su slug.
     * No requiere autenticación.
     *
     * @unauthenticated
     */
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

    /**
     * Categorías del catálogo
     *
     * Lista todas las categorías de productos de una tienda.
     * Útil para construir filtros en el catálogo.
     *
     * @unauthenticated
     */
    public function categorias(string $slug): JsonResponse
    {
        $tienda = Tienda::where('slug', $slug)->where('estado', true)->firstOrFail();

        $categorias = Categoria::withoutGlobalScopes()
            ->where('tienda_id', $tienda->id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($categorias);
    }

    /**
     * Listado de productos
     *
     * Retorna los productos disponibles (con stock > 0) de una tienda.
     * Soporta filtro por categoría, búsqueda por texto y ordenamiento.
     *
     * @unauthenticated
     * @queryParam categoria_id integer Filtrar por ID de categoría. Example: 3
     * @queryParam q string Buscar por nombre o SKU. Example: cerveza
     * @queryParam sort string Ordenamiento: `nombre` (default), `precio_asc`, `precio_desc`. Example: precio_asc
     * @queryParam per_page integer Resultados por página (máx. 50, default 12). Example: 24
     * @queryParam page integer Número de página. Example: 1
     */
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
            'imagen_url'    => $p->imagen_url,
            'categoria'     => $p->categoria ? ['id' => $p->categoria->id, 'nombre' => $p->categoria->nombre] : null,
            'atributos'     => $p->atributos->map(fn($a) => ['nombre' => $a->nombre, 'valor' => $a->valor]),
        ]);

        return response()->json($productos);
    }

    /**
     * Detalle de un producto
     *
     * Retorna la información completa de un producto disponible, incluyendo
     * categoría y atributos adicionales (talla, color, etc.).
     *
     * @unauthenticated
     */
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
            'imagen_url'    => $producto->imagen_url,
            'categoria'     => $producto->categoria ? ['id' => $producto->categoria->id, 'nombre' => $producto->categoria->nombre] : null,
            'atributos'     => $producto->atributos->map(fn($a) => ['nombre' => $a->nombre, 'valor' => $a->valor]),
        ]);
    }

    /**
     * Vendedores del catálogo
     *
     * Lista los vendedores de la tienda que están visibles en el catálogo público
     * y tienen número de WhatsApp configurado. Usado para el selector de vendedor
     * al hacer un pedido por WhatsApp.
     *
     * @unauthenticated
     */
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
