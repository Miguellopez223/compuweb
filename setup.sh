#!/usr/bin/env bash
set -e

# ─── Colores ──────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; RESET='\033[0m'

echo ""
echo -e "${CYAN}${BOLD} ╔══════════════════════════════════════════╗${RESET}"
echo -e "${CYAN}${BOLD} ║         CompuWeb — Setup Linux            ║${RESET}"
echo -e "${CYAN}${BOLD} ║     Requiere PHP, Composer y MySQL         ║${RESET}"
echo -e "${CYAN}${BOLD} ╚══════════════════════════════════════════╝${RESET}"
echo ""

# ─── Verificar dependencias ───────────────────────────────────────
for cmd in php composer mysql; do
    if ! command -v "$cmd" &>/dev/null; then
        echo -e "${RED}[ERROR]${RESET} '$cmd' no encontrado."
        case $cmd in
            php)      echo "        Instala: sudo apt install php php-mbstring php-xml php-mysql php-zip" ;;
            composer) echo "        Instala: https://getcomposer.org/download/" ;;
            mysql)    echo "        Instala: sudo apt install mysql-client" ;;
        esac
        exit 1
    fi
done

# ─── Datos de conexion MySQL ──────────────────────────────────────
echo -e "${BOLD}Configuracion de base de datos MySQL:${RESET}"
echo "(Presiona Enter para usar el valor por defecto [entre corchetes])"
echo ""

read -p "  Host     [127.0.0.1]: " DB_HOST;     DB_HOST=${DB_HOST:-127.0.0.1}
read -p "  Puerto   [3306]: "       DB_PORT;     DB_PORT=${DB_PORT:-3306}
read -p "  Base de datos [compuweb]: " DB_DATABASE; DB_DATABASE=${DB_DATABASE:-compuweb}
read -p "  Usuario  [root]: "       DB_USERNAME; DB_USERNAME=${DB_USERNAME:-root}
read -s -p "  Contrasena []: "      DB_PASSWORD; echo ""

echo ""
echo -e "─────────────────────────────────────────────"
echo -e " Configuracion elegida:"
echo -e "   Host:          ${DB_HOST}:${DB_PORT}"
echo -e "   Base de datos: ${DB_DATABASE}"
echo -e "   Usuario:       ${DB_USERNAME}"
echo -e "─────────────────────────────────────────────"
echo ""

# ─── Crear base de datos si no existe ────────────────────────────
echo -e "${YELLOW}[1/7]${RESET} Creando base de datos si no existe..."
MYSQL_PWD_OPT=""
[ -n "$DB_PASSWORD" ] && MYSQL_PWD_OPT="-p${DB_PASSWORD}"
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" $MYSQL_PWD_OPT \
    -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null \
    || echo -e "${YELLOW}[ADVERTENCIA]${RESET} No se pudo crear la BD. Creala manualmente si no existe."

# ─── Copiar .env ──────────────────────────────────────────────────
echo -e "${YELLOW}[2/7]${RESET} Configurando archivo .env..."
[ ! -f ".env" ] && cp .env.example .env

# Función para reemplazar o agregar en .env
set_env() {
    local key="$1" val="$2"
    if grep -qE "^#?\\s*${key}=" .env; then
        sed -i "s|^#\?[[:space:]]*${key}=.*|${key}=${val}|" .env
    else
        echo "${key}=${val}" >> .env
    fi
}

set_env DB_CONNECTION mysql
set_env DB_HOST       "$DB_HOST"
set_env DB_PORT       "$DB_PORT"
set_env DB_DATABASE   "$DB_DATABASE"
set_env DB_USERNAME   "$DB_USERNAME"
set_env DB_PASSWORD   "$DB_PASSWORD"

# ─── Composer install ─────────────────────────────────────────────
echo -e "${YELLOW}[3/7]${RESET} Instalando dependencias PHP..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# ─── App key ──────────────────────────────────────────────────────
echo -e "${YELLOW}[4/7]${RESET} Generando clave de aplicacion..."
php artisan key:generate --force

# ─── Migraciones ──────────────────────────────────────────────────
echo -e "${YELLOW}[5/7]${RESET} Ejecutando migraciones..."
php artisan migrate --force

# ─── Seeders ──────────────────────────────────────────────────────
echo -e "${YELLOW}[6/7]${RESET} Cargando datos demo (TercerTiempoSeeder)..."
php artisan db:seed --force || echo -e "${YELLOW}[ADVERTENCIA]${RESET} El seeder tuvo un error. Revisa manualmente."

# ─── Storage link ─────────────────────────────────────────────────
echo -e "${YELLOW}[7/7]${RESET} Creando enlace de almacenamiento..."
php artisan storage:link 2>/dev/null || true

# ─── Listo ────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD} ╔══════════════════════════════════════════════════╗${RESET}"
echo -e "${GREEN}${BOLD} ║  ✓ Instalacion completada                        ║${RESET}"
echo -e "${GREEN}${BOLD} ║                                                  ║${RESET}"
echo -e "${GREEN}${BOLD} ║  Levanta el servidor:                            ║${RESET}"
echo -e "${GREEN}${BOLD} ║    php artisan serve                             ║${RESET}"
echo -e "${GREEN}${BOLD} ║    → http://localhost:8000                       ║${RESET}"
echo -e "${GREEN}${BOLD} ║                                                  ║${RESET}"
echo -e "${GREEN}${BOLD} ║  Credenciales demo (tienda Tercer Tiempo):       ║${RESET}"
echo -e "${GREEN}${BOLD} ║    Admin:    alejandro@tercertiempo.com           ║${RESET}"
echo -e "${GREEN}${BOLD} ║    Password: password                            ║${RESET}"
echo -e "${GREEN}${BOLD} ╚══════════════════════════════════════════════════╝${RESET}"
echo ""
