#!/bin/bash

# ==============================================================
# Event Ticketing WA Gateway - VPS Installation Script
# Supports: Ubuntu 20.04 / 22.04 / 24.04
# ==============================================================

set -e  # Exit immediately if a command fails

# ============================================================
# COLORS
# ============================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# ============================================================
# CONFIGURATION - EDIT SESUAI KEBUTUHAN
# ============================================================
APP_DIR="/var/www/event-ticketing"
APP_URL="http://18.143.76.195"           # Ganti dengan domain/IP VPS Anda
DB_DATABASE="event_ticketing"
DB_USERNAME="ticketing_user"
DB_PASSWORD="tiket123"   # Password random, bisa diganti manual
DB_ROOT_PASSWORD="tiket123"
WA_API_KEY="tiket123"    # API Key random untuk WA Gateway
WA_GATEWAY_PORT=3001
LARAVEL_PORT=8000
NODE_VERSION=20

# ============================================================
# HELPER FUNCTIONS
# ============================================================
print_header() {
    echo ""
    echo -e "${BLUE}╔══════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║  ${CYAN}$1${BLUE}  ║${NC}"
    echo -e "${BLUE}╚══════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_info() {
    echo -e "${YELLOW}[i]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗] ERROR:${NC} $1"
}

check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Script ini harus dijalankan sebagai root!"
        echo "  Gunakan: sudo bash install.sh"
        exit 1
    fi
}

confirm() {
    read -rp "$(echo -e "${YELLOW}[?]${NC} $1 [y/N] ")" response
    case "$response" in
        [yY][eE][sS]|[yY]) return 0 ;;
        *) return 1 ;;
    esac
}

# ============================================================
# MULAI INSTALASI
# ============================================================
clear
echo ""
echo -e "${CYAN}"
echo "  ███████╗██╗   ██╗███████╗███╗   ██╗████████╗"
echo "  ██╔════╝██║   ██║██╔════╝████╗  ██║╚══██╔══╝"
echo "  █████╗  ██║   ██║█████╗  ██╔██╗ ██║   ██║   "
echo "  ██╔══╝  ╚██╗ ██╔╝██╔══╝  ██║╚██╗██║   ██║   "
echo "  ███████╗ ╚████╔╝ ███████╗██║ ╚████║   ██║   "
echo "  ╚══════╝  ╚═══╝  ╚══════╝╚═╝  ╚═══╝   ╚═╝   "
echo ""
echo "  TICKETING  ×  WHATSAPP  GATEWAY"
echo -e "${NC}"
echo -e "  ${YELLOW}VPS Auto-Installer v1.0${NC}"
echo ""

check_root

print_info "Konfigurasi yang akan digunakan:"
echo "  App Directory  : $APP_DIR"
echo "  App URL        : $APP_URL"
echo "  Database       : $DB_DATABASE"
echo "  DB User        : $DB_USERNAME"
echo "  DB Password    : $DB_PASSWORD"
echo "  WA Gateway Key : $WA_API_KEY"
echo "  Node.js        : v$NODE_VERSION"
echo ""

if ! confirm "Lanjutkan instalasi?"; then
    echo "Instalasi dibatalkan."
    exit 0
fi

# Simpan credentials ke file
CREDS_FILE="$HOME/ticketing-credentials.txt"
cat > "$CREDS_FILE" << EOF
========================================
EVENT TICKETING - CREDENTIALS
Generated: $(date)
========================================

APP URL          : $APP_URL
APP Directory    : $APP_DIR

Database Host    : 127.0.0.1
Database Name    : $DB_DATABASE
Database User    : $DB_USERNAME
Database Pass    : $DB_PASSWORD
MySQL Root Pass  : $DB_ROOT_PASSWORD

WA Gateway Key   : $WA_API_KEY
WA Gateway Port  : $WA_GATEWAY_PORT
Laravel Port     : $LARAVEL_PORT

Default Login:
  Admin Email    : admin@example.com
  Admin Pass     : password
  Staff Email    : staff@example.com
  Staff Pass     : password
========================================
EOF
chmod 600 "$CREDS_FILE"
print_step "Credentials disimpan di: $CREDS_FILE"

# ============================================================
# STEP 1: Update System
# ============================================================
print_header "STEP 1: Update System"

apt-get update -y
apt-get upgrade -y
apt-get install -y \
    curl \
    wget \
    git \
    unzip \
    zip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release \
    ufw \
    nano \
    htop \
    openssl

print_step "System packages berhasil diinstall"

# ============================================================
# STEP 2: Install PHP 8.3
# ============================================================
print_header "STEP 2: Install PHP 8.3"

add-apt-repository ppa:ondrej/php -y
apt-get update -y
apt-get install -y \
    php8.3 \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-intl \
    php8.3-readline \
    php8.3-tokenizer \
    php8.3-dom \
    php8.3-fileinfo

php -v
print_step "PHP 8.3 berhasil diinstall"

# ============================================================
# STEP 3: Install Composer
# ============================================================
print_header "STEP 3: Install Composer"

if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm /tmp/composer-setup.php
fi

composer --version
print_step "Composer berhasil diinstall"

# ============================================================
# STEP 4: Install Node.js
# ============================================================
print_header "STEP 4: Install Node.js $NODE_VERSION"

if ! command -v node &> /dev/null; then
    curl -fsSL "https://deb.nodesource.com/setup_${NODE_VERSION}.x" | bash -
    apt-get install -y nodejs
fi

node -v
npm -v
print_step "Node.js berhasil diinstall"

# Install PM2 untuk process management
npm install -g pm2
print_step "PM2 berhasil diinstall"

# ============================================================
# Install Chromium untuk WhatsApp Gateway (puppeteer)
# ============================================================
print_info "Install Chromium + puppeteer dependencies..."
apt-get install -y \
    chromium-browser \
    ca-certificates \
    fonts-liberation \
    libasound2 \
    libasound2t64 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libc6 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgbm1 \
    libgcc1 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libstdc++6 \
    libx11-6 \
    libx11-xcb1 \
    libxcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxss1 \
    libxtst6 \
    lsb-release \
    wget \
    xdg-utils || true

print_step "Chromium berhasil diinstall"

# ============================================================
# STEP 5: Install MySQL 8.0
# ============================================================
print_header "STEP 5: Install MySQL 8.0"

apt-get install -y mysql-server

# Start MySQL
systemctl start mysql
systemctl enable mysql

# Secure MySQL & create database
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_ROOT_PASSWORD';" 2>/dev/null || true
mysql -uroot -p"$DB_ROOT_PASSWORD" << MYSQL_EOF
CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'localhost';
FLUSH PRIVILEGES;
MYSQL_EOF

print_step "MySQL 8.0 dan database '$DB_DATABASE' berhasil disiapkan"

# ============================================================
# STEP 6: Install Nginx
# ============================================================
print_header "STEP 6: Install Nginx"

apt-get install -y nginx

# Konfigurasi Nginx untuk Laravel
cat > /etc/nginx/sites-available/event-ticketing << NGINX_EOF
server {
    listen 80;
    server_name _;
    root $APP_DIR/apps/api/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_EOF

# Enable site
ln -sf /etc/nginx/sites-available/event-ticketing /etc/nginx/sites-enabled/event-ticketing
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl start nginx
systemctl enable nginx

print_step "Nginx berhasil dikonfigurasi"

# ============================================================
# STEP 7: Clone / Copy Project
# ============================================================
print_header "STEP 7: Setup Project Files"

mkdir -p "$APP_DIR"

# Cek apakah sudah ada project atau perlu clone
if [ -f "$APP_DIR/apps/api/artisan" ] || [ -f "$APP_DIR/artisan" ]; then
    print_info "Project sudah ada di $APP_DIR, skip clone..."
else
    print_info "Masukkan URL Git repository Anda:"
    read -rp "  Git URL (kosongkan jika copy manual): " GIT_URL

    if [ -n "$GIT_URL" ]; then
        # Clone ke tmp dulu agar bisa deteksi struktur
        CLONE_TMP="/tmp/repo-clone-$$"
        git clone "$GIT_URL" "$CLONE_TMP"
        print_step "Repository berhasil di-clone ke temp"

        # --- Auto-detect struktur repo ---
        if [ -f "$CLONE_TMP/artisan" ]; then
            # Repo langsung berisi Laravel di root (tanpa apps/api)
            print_info "Deteksi: Laravel ada di root repo"
            mkdir -p "$APP_DIR/apps"
            mv "$CLONE_TMP" "$APP_DIR/apps/api"
            # Buat folder wa-gateway kosong jika tidak ada
            mkdir -p "$APP_DIR/apps/wa-gateway"

        elif [ -f "$CLONE_TMP/apps/api/artisan" ]; then
            # Repo sudah monorepo standar: apps/api + apps/wa-gateway
            print_info "Deteksi: Monorepo standar (apps/api)"
            cp -r "$CLONE_TMP/". "$APP_DIR/"
            rm -rf "$CLONE_TMP"

        else
            # Coba cari artisan secara rekursif 2 levell
            ARTISAN_PATH=$(find "$CLONE_TMP" -maxdepth 5 -name "artisan" | head -1)
            if [ -n "$ARTISAN_PATH" ]; then
                LARAVEL_ROOT=$(dirname "$ARTISAN_PATH")
                print_info "Deteksi: Laravel ditemukan di $LARAVEL_ROOT"
                mkdir -p "$APP_DIR/apps"
                mv "$LARAVEL_ROOT" "$APP_DIR/apps/api"
                # Coba pindahkan wa-gateway jika ada
                WA_PATH=$(find "$CLONE_TMP" -maxdepth 5 -name "index.js" -path "*/wa-gateway/*" | head -1)
                if [ -n "$WA_PATH" ]; then
                    mv "$(dirname "$WA_PATH")" "$APP_DIR/apps/wa-gateway"
                else
                    mkdir -p "$APP_DIR/apps/wa-gateway"
                fi
                rm -rf "$CLONE_TMP"
            else
                print_error "Tidak dapat menemukan file artisan di repository!"
                print_info "Isi repo yang di-clone:"
                ls -la "$CLONE_TMP/"
                rm -rf "$CLONE_TMP"
                exit 1
            fi
        fi
        print_step "Project berhasil disiapkan di $APP_DIR"
    else
        print_info "Silakan copy project Anda ke: $APP_DIR"
        echo ""
        echo "Struktur yang diharapkan:"
        echo "  $APP_DIR/"
        echo "  ├── apps/"
        echo "  │   ├── api/          <- Laravel (ada file artisan)"
        echo "  │   └── wa-gateway/   <- Node.js"
        echo ""
        read -rp "Tekan ENTER setelah project di-copy..."
    fi
fi

# ============================================================
# STEP 8: Setup Laravel
# ============================================================
print_header "STEP 8: Setup Laravel Application"

# Pastikan path Laravel benar
if [ ! -f "$APP_DIR/apps/api/artisan" ]; then
    print_error "File artisan tidak ditemukan di $APP_DIR/apps/api/"
    print_info "Isi direktori $APP_DIR:"
    ls -la "$APP_DIR/" 2>/dev/null || true
    print_info "Isi direktori $APP_DIR/apps (jika ada):"
    ls -la "$APP_DIR/apps/" 2>/dev/null || true
    exit 1
fi

cd "$APP_DIR/apps/api"

# Setup .env
cp .env.example .env

# Update .env values
sed -i "s|APP_ENV=local|APP_ENV=production|g" .env
sed -i "s|APP_DEBUG=true|APP_DEBUG=false|g" .env
sed -i "s|APP_URL=http://localhost:8000|APP_URL=$APP_URL|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
sed -i "s|DB_USERNAME=root|DB_USERNAME=$DB_USERNAME|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
sed -i "s|WA_GATEWAY_URL=.*|WA_GATEWAY_URL=http://127.0.0.1:$WA_GATEWAY_PORT|g" .env
sed -i "s|WA_GATEWAY_KEY=.*|WA_GATEWAY_KEY=$WA_API_KEY|g" .env

# Install Composer dependencies (production)
composer install --no-dev --optimize-autoloader

# Generate App Key
php artisan key:generate --force

# Run Migrations & Seeders
print_info "Menjalankan migration dan seeder..."
php artisan migrate --force
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Build assets (Vite)
npm install
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data "$APP_DIR/apps/api"
chown -R www-data:www-data "$APP_DIR/apps/api/storage"
chown -R www-data:www-data "$APP_DIR/apps/api/bootstrap/cache"
chmod -R 775 "$APP_DIR/apps/api/storage"
chmod -R 775 "$APP_DIR/apps/api/bootstrap/cache"

print_step "Laravel berhasil dikonfigurasi"

# ============================================================
# STEP 9: Setup WhatsApp Gateway
# ============================================================
print_header "STEP 9: Setup WhatsApp Gateway"

# Cek apakah folder wa-gateway ada dan punya index.js
if [ -f "$APP_DIR/apps/wa-gateway/index.js" ]; then
    cd "$APP_DIR/apps/wa-gateway"

    # Setup .env
    if [ -f ".env.example" ]; then
        cp .env.example .env
    else
        # Buat .env manual jika tidak ada
        echo "PORT=$WA_GATEWAY_PORT" > .env
        echo "API_KEY=$WA_API_KEY" >> .env
    fi
    sed -i "s|PORT=.*|PORT=$WA_GATEWAY_PORT|g" .env
    sed -i "s|API_KEY=.*|API_KEY=$WA_API_KEY|g" .env

    # Install Node.js dependencies
    npm install --omit=dev
    print_step "WhatsApp Gateway berhasil dikonfigurasi"
else
    print_info "wa-gateway tidak ditemukan, skip STEP 9..."
    print_info "Anda bisa setup manual di: $APP_DIR/apps/wa-gateway/"
fi

# ============================================================
# STEP 10: Setup PM2 Process Manager
# ============================================================
print_header "STEP 10: Setup PM2 Services"

# PM2 ecosystem config
cat > "$APP_DIR/ecosystem.config.js" << PM2_EOF
module.exports = {
  apps: [
    {
      name: 'wa-gateway',
      cwd: '$APP_DIR/apps/wa-gateway',
      script: 'index.js',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '500M',
      env: {
        NODE_ENV: 'production',
        PORT: $WA_GATEWAY_PORT,
        API_KEY: '$WA_API_KEY'
      },
      error_file: '/var/log/pm2/wa-gateway-error.log',
      out_file: '/var/log/pm2/wa-gateway-out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss'
    }
  ]
};
PM2_EOF

mkdir -p /var/log/pm2

# Start WA Gateway dengan PM2
pm2 start "$APP_DIR/ecosystem.config.js"
pm2 save

# Setup PM2 sistemd startup
pm2 startup systemd -u root --hp /root
pm2 save

print_step "PM2 berhasil dikonfigurasi"

# ============================================================
# STEP 11: Setup Laravel Queue Worker dengan Supervisor
# ============================================================
print_header "STEP 11: Setup Queue Worker (Supervisor)"

apt-get install -y supervisor

cat > /etc/supervisor/conf.d/ticketing-queue.conf << SUPERVISOR_EOF
[program:ticketing-queue]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/apps/api/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/ticketing-queue.log
stopwaitsecs=3600
SUPERVISOR_EOF

supervisorctl reread
supervisorctl update
supervisorctl start ticketing-queue:*

systemctl enable supervisor
systemctl start supervisor

print_step "Queue Worker berhasil dikonfigurasi"

# ============================================================
# STEP 12: Setup Firewall (UFW)
# ============================================================
print_header "STEP 12: Setup Firewall"

ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp
# WA Gateway port hanya internal (tidak dibuka ke publik)
# ufw allow $WA_GATEWAY_PORT/tcp

ufw --force enable

print_step "Firewall berhasil dikonfigurasi"

# ============================================================
# STEP 13: Health Check
# ============================================================
print_header "STEP 13: Health Check"

sleep 3

# Check Nginx
if systemctl is-active --quiet nginx; then
    print_step "Nginx: RUNNING"
else
    print_error "Nginx: STOPPED"
fi

# Check MySQL
if systemctl is-active --quiet mysql; then
    print_step "MySQL: RUNNING"
else
    print_error "MySQL: STOPPED"
fi

# Check PHP-FPM
if systemctl is-active --quiet php8.3-fpm; then
    print_step "PHP-FPM: RUNNING"
else
    print_error "PHP-FPM: STOPPED"
fi

# Check PM2
if pm2 list | grep -q "wa-gateway"; then
    print_step "WA Gateway (PM2): RUNNING"
else
    print_error "WA Gateway (PM2): STOPPED"
fi

# Check Supervisor Queue
if supervisorctl status ticketing-queue:* | grep -q "RUNNING"; then
    print_step "Queue Worker (Supervisor): RUNNING"
else
    print_error "Queue Worker (Supervisor): STOPPED"
fi

# ============================================================
# SELESAI
# ============================================================
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        INSTALASI SELESAI!                            ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${CYAN}App URL     :${NC} $APP_URL"
echo -e "  ${CYAN}Credentials :${NC} $CREDS_FILE"
echo ""
echo -e "  ${YELLOW}LANGKAH SELANJUTNYA:${NC}"
echo ""
echo "  1. Scan QR Code WhatsApp:"
echo -e "     ${CYAN}pm2 logs wa-gateway${NC}"
echo ""
echo "  2. Cek status semua service:"
echo -e "     ${CYAN}pm2 status && supervisorctl status${NC}"
echo ""
echo "  3. (Opsional) Install SSL dengan Certbot:"
echo -e "     ${CYAN}apt install certbot python3-certbot-nginx -y${NC}"
echo -e "     ${CYAN}certbot --nginx -d your-domain.com${NC}"
echo ""
echo "  4. Update domain di .env Laravel:"
echo -e "     ${CYAN}nano $APP_DIR/apps/api/.env${NC}"
echo ""
echo -e "${YELLOW}⚠ PENTING: Simpan file credentials di: $CREDS_FILE${NC}"
echo ""
