# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**CompuWeb** is a Laravel 13 monolithic SaaS platform for multi-tenancy retail inventory and Point-of-Sale management, designed to solve the "silencio informativo" (informational silence) — the gap between a store's physical inventory and what customers see in its digital catalog. Targeted at small/micro enterprises (MyPEs) that currently manage inventory via static PDFs or manual records.

The platform bridges the physical warehouse to a dynamic public storefront, and closes sales through WhatsApp conversational commerce. All stock deductions are atomic transactions, so the catalog always reflects real-time inventory.

**University project** — Universidad Privada Boliviana, Facultad de Ingeniería.
**Team**: Miguel Angel Lopez Arispe, Ignacio Rodrigo Daza Reyes, Santiago Matías Daens Hoyos, Alan Marcelo Villavicencio Ponce.
**Instructor**: Ing. José Alejandro Rodríguez Zegada.

### Key Features
- **Multi-tenancy**: Multiple stores (tiendas) isolated by `tienda_id`, single DB instance
- **Inventory Management**: Products with dynamic attributes, variable units of measure, stock movement audit trail
- **Real-time Sync**: Atomic transactions — a sale immediately deducts stock and marks products as agotado (out-of-stock) in the public catalog
- **Public Catalog**: Slug-based storefront, no auth required, consumes internal API
- **WhatsApp Commerce**: Customer selects a seller from the available list; system generates a structured WhatsApp deep-link
- **Role-Based Access**: Admin and Vendedor roles per store, enforced by OAuth2 + AdminOnly middleware
- **Analytics**: Gross profit (`Σ(PrecioVenta - PrecioCosto)`) and inventory valuation (`Σ(StockDisponible * PrecioCosto)`) reports
- **PDF Receipts**: Invoice generation with NIT/factura fields via DomPDF

## Tech Stack

- **Framework**: Laravel 13
- **Frontend**: Livewire 4.3 (reactive components) + Vite
- **Database**: SQLite (local), MySQL (production)
- **Authentication**: Laravel Passport (API) + Session (web)
- **API Documentation**: Dedoc Scramble
- **PDF Generation**: BarryVDH DomPDF
- **Code Quality**: Laravel Pint
- **Testing**: PHPUnit
- **Task Queue**: Laravel Queue (with Pail for monitoring)

## Development Setup

### Initial Setup
Run the setup script for your OS:
- **Windows**: `setup.bat`
- **Linux/Mac**: `bash setup.sh`

These scripts:
1. Create MySQL database (prompts for credentials)
2. Copy `.env` and configure database connection
3. Install Composer and npm dependencies
4. Generate app key, run migrations, and seed demo data
5. Create storage symlink for file uploads

### Running the Application

**Development mode** (all services in one terminal):
```bash
npm run dev
```
This concurrently runs:
- PHP artisan serve (port 8000)
- Queue listener (background jobs)
- Pail (log aggregation)
- Vite dev server (frontend hot reload)

**Individual services**:
```bash
php artisan serve                    # Web server
php artisan queue:listen --tries=1   # Background jobs
php artisan pail --timeout=0         # Log watcher
npm run dev                          # Vite frontend
```

### Database

**SQLite** (default for local development):
- File: `database/database.sqlite`
- Auto-created during setup
- Suitable for single-user development

**MySQL** (production):
- Configure in `.env`: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Run migrations: `php artisan migrate`

**Migrations**:
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:refresh      # Reset and re-run (development only)
php artisan migrate:fresh --seed # Fresh + demo data
```

## Project Structure

### Core Directories

**`app/Models/`** — Eloquent models
- `Tienda` — Store/tenant (multitenancy root)
- `User` — Users belong to a Tienda
- `Producto` — Products with images and attributes
- `Categoria` — Product categories
- `Venta` — Sales orders
- `DetalleVenta` — Line items in a sale
- `MovimientoInventario` — Stock movements
- `Cliente` — Sales customers (generic client available)
- Supporting: `AtributoProducto`, `UnidadMedida`

**`app/Http/`**
- `Controllers/` — Web controllers (ReciboController for PDF export)
- `Controllers/Api/` — API controllers (Auth, Ventas, Reportes, Public)
- `Middleware/` — `AdminOnly` for role enforcement

**`app/Livewire/`** — Livewire components for web UI
- `Auth/Login` — Login form
- `Dashboard` — Home/dashboard
- `Productos/`, `Categorias/`, `Movimientos/`, `Ventas/`, `Usuarios/`, `Reportes/`, `Configuracion/` — CRUD modules
- `Catalogo/` — Public storefront (Index, Detalle)

**`routes/`**
- `web.php` — Web routes (Livewire, session-based auth)
- `api.php` — API routes (Passport token auth, RESTful)

**`database/migrations/`** — Schema definitions
- Core tables: tiendas, users, productos, categorias, ventas, detalle_ventas, movimientos_inventario
- OAuth tables (Passport): oauth_clients, oauth_access_tokens, etc.
- Facturacion fields: nit, numero_factura for invoice generation

**`database/seeders/`**
- `TercerTiempoSeeder` — Demo data for testing (complete store with products, users, sales)
- Run: `php artisan db:seed`

**`config/`** — Application configuration
- `app.php` — App settings
- Standard Laravel configs (database, cache, queue, auth, etc.)

**`resources/views/`** — Blade templates (minimal; Livewire handles most UI)
**`resources/js/`** — Frontend JavaScript entry points
**`public/`** — Web root (CSS, JS, images after build)

### Key Patterns

#### Multitenancy
- **Model**: Soft multitenancy via `tienda_id` foreign key
- **Auth**: User belongs_to Tienda; queries are scoped by user's tienda
- **Routes**: 
  - Web: Implicit from authenticated user's tienda
  - API: Public catalog scoped by `{slug}` parameter
- **No explicit tenant middleware needed** — User's tienda_id is primary context

#### Role-Based Authorization
- `User.role`: 'admin' or 'vendedor'
- `AdminOnly` middleware checks `auth()->user()->role === 'admin'`
- Web: Applied to admin routes (usuarios, reportes, configuracion)
- API: Applied to admin endpoints (reportes)

#### API Authentication
- **Grant Type**: Personal Access Token (Passport)
- **Endpoint**: `POST /api/auth/login` with email/password
- **Response**: Returns `access_token` (Bearer token)
- **Usage**: `Authorization: Bearer {token}` header on protected requests
- **Validation**: `middleware('auth:api')` on protected routes

#### API Access Levels

| Nivel    | Requiere         | Puede hacer                              |
|----------|-----------------|------------------------------------------|
| Público  | Sin token        | Navegar catálogo de una tienda           |
| Vendedor | Token válido     | Registrar y consultar ventas             |
| Admin    | Token + rol admin| Reportes de ventas e inventario          |

#### Public Catalog
- **Route**: `GET /api/tiendas/{slug}/...` (no authentication)
- **Endpoints**:
  - `GET /api/tiendas/{slug}/` — Store info
  - `GET /api/tiendas/{slug}/categorias` — Categories
  - `GET /api/tiendas/{slug}/productos` — Products (paginated)
  - `GET /api/tiendas/{slug}/productos/{id}` — Product detail
  - `GET /api/tiendas/{slug}/vendedores` — Seller list with WhatsApp numbers
- **Slug**: Unique identifier per store (not ID) — e.g. `tercer-tiempo`

#### WhatsApp Integration Pattern
The catalog exposes sellers (`visible_catalogo = true`) with their `whatsapp_number`. When a customer finalizes cart selection:
1. Frontend fetches `/api/tiendas/{slug}/vendedores` to show seller picker
2. Customer selects a seller
3. Frontend builds a WhatsApp deep-link with the structured cart content:
   ```
   https://wa.me/{whatsapp_number}?text={encoded_cart_message}
   ```
4. Customer is redirected to WhatsApp to complete the sale conversationally
- No payment gateway is integrated — all payments are handled in the WhatsApp conversation (QR, cash, etc.)

#### Business Logic Formulas
The analytics reports calculate:
```
Utilidad Bruta          = Σ(PrecioVenta - PrecioCosto)  [per DetalleVenta]
Valorización Inventario = Σ(StockDisponible * PrecioCosto) [per Producto]
```

## Commands

### Common Development Tasks

**Code Quality**:
```bash
composer pint              # Format code (Laravel Pint)
composer pint --check      # Check formatting without changes
```

**Database**:
```bash
php artisan migrate                    # Run pending migrations
php artisan make:migration NAME        # Create new migration
php artisan seed --class=TercerTiempoSeeder  # Run specific seeder
php artisan tinker                     # Interactive PHP shell (for debugging)
```

**Testing**:
```bash
composer test              # Run all tests (alias for: php artisan test)
php artisan test --filter=NameTest     # Run specific test
php artisan test tests/Unit/ModelTest.php  # Run specific file
```

**Cache/Config**:
```bash
php artisan config:clear              # Clear cached config
php artisan cache:clear               # Clear application cache
php artisan view:clear                # Clear compiled views
```

**API Documentation**:
- **Auto-generated docs**: Scramble generates OpenAPI spec from routes
- **Access**: Often available at `/api/documentation` (if configured)

**File Management**:
```bash
php artisan storage:link   # Create public/storage symlink (for uploaded images)
```

### Frontend

**Build**:
```bash
npm run dev    # Development (watch mode with HMR)
npm run build  # Production build
```

**Dependencies**:
```bash
npm install                # Install npm packages
npm install package-name   # Add new package
```

## Architecture Decisions

### Why Livewire + Blade?
- Reactive, interactive UI without writing JavaScript
- Simple state management per component
- Tight integration with Laravel backend
- Faster for CRUD operations than traditional SPA

### Why Soft Multitenancy?
- Single database, partitioned by tienda_id
- Simpler operational model (one database to manage)
- Easier migration path (no data isolation concerns for small stores)
- Lower infrastructure cost

### Why Passport for API?
- OAuth 2.0 compliance
- Personal Access Token flow is simple for mobile/desktop clients
- Built-in revocation and scope management
- Well-integrated with Laravel

### Why SQLite for Local Development?
- Zero configuration
- File-based (no database server needed)
- Perfect for single-developer workflow
- Production uses MySQL for multi-user concurrency

## Common Workflows

### Adding a New CRUD Feature

1. **Create Model**:
   ```bash
   php artisan make:model ModelName -m  # With migration
   ```

2. **Define Schema** in migration:
   ```php
   Schema::create('table_name', function (Blueprint $table) {
       $table->id();
       $table->foreignId('tienda_id')->constrained();  // Always include tienda_id
       $table->string('name');
       $table->timestamps();
   });
   ```

3. **Create Livewire Component**:
   ```bash
   php artisan make:livewire Models/NameIndex  # For list view
   ```

4. **Add Route** in `routes/web.php`:
   ```php
   Route::get('/resource-name', NameIndex::class)->name('resource-name');
   ```

5. **Implement Component** (handle list, create, edit, delete)

6. **Test**: `php artisan test` to verify

### Adding an API Endpoint

1. **Create Controller**:
   ```bash
   php artisan make:controller Api/ResourceController
   ```

2. **Define Routes** in `routes/api.php`:
   ```php
   Route::middleware('auth:api')->group(function () {
       Route::apiResource('resources', ResourceController::class);
   });
   ```

3. **Implement Controller** with standard RESTful methods (index, store, show, update, destroy)

4. **Test with curl or Postman**:
   ```bash
   curl -H "Authorization: Bearer TOKEN" http://localhost:8000/api/resources
   ```

### Generating a PDF Receipt

- See `ReciboController.php` for example
- Uses DomPDF (barryvdh/laravel-dompdf)
- Route: `GET /ventas/{venta}/recibo`
- Generates invoice from Venta model data

## Important Notes

- **tienda_id is mandatory**: Every domain record must carry `tienda_id`. This is the only tenant isolation mechanism — there is no tenant middleware; queries must scope by the authenticated user's `tienda_id`.
- **Atomic stock deduction**: When registering a sale, stock must be decremented inside a DB transaction. If the transaction fails, nothing should commit. This is the core integrity guarantee of the system.
- **Silencio informativo is the enemy**: The system exists to prevent the state where a product shows as available in the catalog but is out of stock physically. Any feature touching inventory or sales must maintain this real-time consistency.
- **Migrations are committed**: Always commit migrations; they are the source of truth for schema.
- **Demo seeder**: `TercerTiempoSeeder` — a fully populated bar/restaurant store ("Tercer Tiempo") with products, categories, users, and sales. Good for testing all features. Admin credentials: `alejandro@tercertiempo.com` / `password`.
- **Livewire vs API**: Livewire for the admin panel (session auth); API for the public catalog and external clients (Passport token auth).
- **Stock tracking**: Every stock change goes through `MovimientoInventario`. Never update `Producto.stock` directly without creating a movement record.
- **Dynamic attributes**: `AtributoProducto` and `UnidadMedida` allow the catalog to serve stores beyond the original tech-store use case (e.g., bars, restaurants).

## Planned Future Features (Not Yet Implemented)
- Payment gateway integration directly in the public catalog (currently all payments are conversational via WhatsApp)
- Native barcode scanner hardware support in the admin panel for faster stock entry

## References

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Laravel Passport](https://laravel.com/docs/13/passport)
- [Eloquent ORM](https://laravel.com/docs/13/eloquent)
