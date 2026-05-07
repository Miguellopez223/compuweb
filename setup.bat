@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul 2>&1

echo.
echo  ╔══════════════════════════════════════════╗
echo  ║         CompuWeb — Setup Windows         ║
echo  ║     Requiere Laragon o XAMPP activo       ║
echo  ╚══════════════════════════════════════════╝
echo.

:: ─── Verificar PHP ───────────────────────────────────────────────
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP no encontrado en PATH.
    echo         Asegurate de que Laragon o XAMPP este corriendo
    echo         y que PHP este en el PATH del sistema.
    pause
    exit /b 1
)

:: ─── Verificar Composer ──────────────────────────────────────────
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer no encontrado en PATH.
    echo         Descargalo desde https://getcomposer.org
    pause
    exit /b 1
)

:: ─── Datos de conexion MySQL ──────────────────────────────────────
echo Configuracion de base de datos MySQL:
echo (Presiona Enter para usar el valor por defecto entre [corchetes])
echo.

set /p DB_HOST="  Host     [127.0.0.1]: "
if "!DB_HOST!"=="" set DB_HOST=127.0.0.1

set /p DB_PORT="  Puerto   [3306]: "
if "!DB_PORT!"=="" set DB_PORT=3306

set /p DB_DATABASE="  Base de datos [compuweb]: "
if "!DB_DATABASE!"=="" set DB_DATABASE=compuweb

set /p DB_USERNAME="  Usuario  [root]: "
if "!DB_USERNAME!"=="" set DB_USERNAME=root

set /p DB_PASSWORD="  Contrasena []: "

echo.
echo ─────────────────────────────────────────────
echo  Configuracion elegida:
echo    Host:     !DB_HOST!:!DB_PORT!
echo    Base de datos: !DB_DATABASE!
echo    Usuario:  !DB_USERNAME!
echo ─────────────────────────────────────────────
echo.

:: ─── Crear base de datos si no existe ────────────────────────────
echo [1/7] Creando base de datos si no existe...
if "!DB_PASSWORD!"=="" (
    mysql -h !DB_HOST! -P !DB_PORT! -u !DB_USERNAME! -e "CREATE DATABASE IF NOT EXISTS `!DB_DATABASE!` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
) else (
    mysql -h !DB_HOST! -P !DB_PORT! -u !DB_USERNAME! -p"!DB_PASSWORD!" -e "CREATE DATABASE IF NOT EXISTS `!DB_DATABASE!` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
)
if %errorlevel% neq 0 (
    echo [ADVERTENCIA] No se pudo crear la BD automaticamente.
    echo              Creala manualmente en phpMyAdmin o MySQL CLI.
)

:: ─── Copiar .env ──────────────────────────────────────────────────
echo [2/7] Configurando archivo .env...
if not exist ".env" (
    copy .env.example .env >nul
)

:: Reemplazar valores en .env usando PowerShell para mayor compatibilidad
powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=mysql' | Set-Content .env"
powershell -Command "$c = Get-Content .env; if ($c -match '^#?\s*DB_HOST=') { $c -replace '^#?\s*DB_HOST=.*', 'DB_HOST=!DB_HOST!' | Set-Content .env } else { Add-Content .env 'DB_HOST=!DB_HOST!' }"
powershell -Command "$c = Get-Content .env; if ($c -match '^#?\s*DB_PORT=') { $c -replace '^#?\s*DB_PORT=.*', 'DB_PORT=!DB_PORT!' | Set-Content .env } else { Add-Content .env 'DB_PORT=!DB_PORT!' }"
powershell -Command "$c = Get-Content .env; if ($c -match '^#?\s*DB_DATABASE=') { $c -replace '^#?\s*DB_DATABASE=.*', 'DB_DATABASE=!DB_DATABASE!' | Set-Content .env } else { Add-Content .env 'DB_DATABASE=!DB_DATABASE!' }"
powershell -Command "$c = Get-Content .env; if ($c -match '^#?\s*DB_USERNAME=') { $c -replace '^#?\s*DB_USERNAME=.*', 'DB_USERNAME=!DB_USERNAME!' | Set-Content .env } else { Add-Content .env 'DB_USERNAME=!DB_USERNAME!' }"
powershell -Command "$c = Get-Content .env; if ($c -match '^#?\s*DB_PASSWORD=') { $c -replace '^#?\s*DB_PASSWORD=.*', 'DB_PASSWORD=!DB_PASSWORD!' | Set-Content .env } else { Add-Content .env 'DB_PASSWORD=!DB_PASSWORD!' }"

:: ─── Composer install ─────────────────────────────────────────────
echo [3/7] Instalando dependencias PHP (composer install)...
composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo [ERROR] Fallo composer install.
    pause
    exit /b 1
)

:: ─── App key ──────────────────────────────────────────────────────
echo [4/7] Generando clave de aplicacion...
php artisan key:generate --force

:: ─── Migraciones ──────────────────────────────────────────────────
echo [5/7] Ejecutando migraciones...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Fallo la migracion. Verifica la conexion a MySQL.
    pause
    exit /b 1
)

:: ─── Seeders ──────────────────────────────────────────────────────
echo [6/7] Cargando datos demo (TercerTiempoSeeder)...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo [ADVERTENCIA] El seeder tuvo un error. Revisa manualmente.
)

:: ─── Storage link ─────────────────────────────────────────────────
echo [7/7] Creando enlace de almacenamiento...
php artisan storage:link >nul 2>&1

:: ─── Listo ────────────────────────────────────────────────────────
echo.
echo  ╔══════════════════════════════════════════════════╗
echo  ║  ✓ Instalacion completada                        ║
echo  ║                                                  ║
echo  ║  Accede al proyecto en Laragon:                  ║
echo  ║    http://compuweb.test                          ║
echo  ║                                                  ║
echo  ║  Credenciales demo (tienda Tercer Tiempo):       ║
echo  ║    Admin:    alejandro@tercertiempo.com           ║
echo  ║    Password: password                            ║
echo  ╚══════════════════════════════════════════════════╝
echo.
pause
