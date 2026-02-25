#!/bin/bash

# ==============================================================
# Event Ticketing - Update / Redeploy Script
# Gunakan script ini untuk update project setelah install.sh
# ==============================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

APP_DIR="/var/www/event-ticketing"

print_step() { echo -e "${GREEN}[✓]${NC} $1"; }
print_info() { echo -e "${YELLOW}[i]${NC} $1"; }
print_error() { echo -e "${RED}[✗]${NC} $1"; }

check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Harus dijalankan sebagai root: sudo bash update.sh"
        exit 1
    fi
}

check_root

echo ""
echo -e "${CYAN}====== EVENT TICKETING - UPDATE SCRIPT ======${NC}"
echo ""

# ============================================================
# 1. Pull dari Git (opsional)
# ============================================================
if [ -d "$APP_DIR/.git" ]; then
    print_info "Mengambil update dari Git..."
    cd "$APP_DIR"
    git pull origin main || git pull origin master
    print_step "Git pull selesai"
fi

# ============================================================
# 2. Update Laravel
# ============================================================
print_info "Update Laravel dependencies..."
cd "$APP_DIR/apps/api"

composer install --no-dev --optimize-autoloader

print_info "Jalankan migration..."
php artisan migrate --force

print_info "Build assets..."
npm install
npm run build

print_info "Clear & rebuild cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

print_info "Set permissions..."
chown -R www-data:www-data "$APP_DIR/apps/api/storage"
chown -R www-data:www-data "$APP_DIR/apps/api/bootstrap/cache"
chmod -R 755 "$APP_DIR/apps/api/storage"
chmod -R 755 "$APP_DIR/apps/api/bootstrap/cache"

print_step "Laravel berhasil di-update"

# ============================================================
# 3. Update WA Gateway
# ============================================================
print_info "Update WA Gateway dependencies..."
cd "$APP_DIR/apps/wa-gateway"
npm install --omit=dev

print_info "Restart WA Gateway..."
pm2 restart wa-gateway || pm2 start "$APP_DIR/ecosystem.config.js"

print_step "WA Gateway berhasil di-update"

# ============================================================
# 4. Restart Queue Worker
# ============================================================
print_info "Restart Queue Worker..."
supervisorctl restart ticketing-queue:*
print_step "Queue Worker berhasil di-restart"

# ============================================================
# 5. Reload Nginx
# ============================================================
nginx -t && systemctl reload nginx
print_step "Nginx berhasil di-reload"

# ============================================================
# SELESAI
# ============================================================
echo ""
echo -e "${GREEN}[✓] Update selesai!${NC}"
echo ""
echo -e "  ${CYAN}Cek WA Gateway logs:${NC} pm2 logs wa-gateway"
echo -e "  ${CYAN}Cek Queue status:${NC}    supervisorctl status"
echo -e "  ${CYAN}Cek Nginx status:${NC}    systemctl status nginx"
echo ""
