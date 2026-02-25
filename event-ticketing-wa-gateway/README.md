# Event Ticketing with WhatsApp Gateway

Sistem ticketing event end-to-end dengan Laravel 11 + Livewire untuk Backend/UI dan Node.js (Express + whatsapp-web.js) sebagai WhatsApp Gateway self-hosted.

## 📁 Struktur Project

```
event-ticketing-wa-gateway/
├── apps/
│   ├── api/                    # Laravel 11 + Livewire UI
│   │   ├── app/
│   │   │   ├── Http/Middleware/
│   │   │   ├── Jobs/
│   │   │   ├── Livewire/
│   │   │   ├── Models/
│   │   │   └── Services/
│   │   ├── database/
│   │   │   ├── migrations/
│   │   │   └── seeders/
│   │   └── resources/views/
│   └── wa-gateway/             # Node.js Express + whatsapp-web.js
│       ├── index.js
│       ├── package.json
│       └── Dockerfile
├── docker-compose.yml
└── README.md
```

## ✨ Fitur

### Admin (role: admin)
- **Events Management**: Buat, edit, hapus event
- **Orders Management**: Lihat order, mark as PAID (generate tiket + kirim WA)
- **Tickets Management**: Lihat semua tiket, filter, download QR, audit history

### Staff Check-in (role: staff, admin)
- **Scanner Support**: Input auto-focus untuk keyboard wedge scanner
- **Approve**: Check-in peserta
- **Edit**: Edit data customer sebelum approve
- **Reject**: Tolak tiket dengan alasan wajib
- **Audit Trail**: Semua perubahan tercatat

### WhatsApp Gateway
- Self-hosted menggunakan whatsapp-web.js
- QR Login via terminal
- Kirim gambar tiket dengan caption otomatis

## 🚀 Setup Local (Tanpa Docker)

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### 1. Clone & Install Laravel

```bash
cd apps/api

# Copy environment
cp .env.example .env

# Edit .env sesuai database local
# DB_DATABASE=event_ticketing
# DB_USERNAME=root
# DB_PASSWORD=

# Install dependencies
composer install
npm install

# Generate key
php artisan key:generate

# Create storage link
php artisan storage:link

# Run migrations & seeders
php artisan migrate:fresh --seed
```

### 2. Setup WhatsApp Gateway

```bash
cd apps/wa-gateway

# Copy environment
cp .env.example .env

# Edit .env
# API_KEY=your-secret-api-key

# Install dependencies
npm install

# Run gateway
npm run dev
```

### 3. Run Laravel

```bash
cd apps/api

# Terminal 1: Run Laravel server
php artisan serve

# Terminal 2: Run queue worker
php artisan queue:work
```

### 4. Login WhatsApp

1. Lihat terminal wa-gateway
2. Scan QR Code yang muncul menggunakan WhatsApp di HP
3. Tunggu sampai muncul "WhatsApp client is ready!"

## 🐳 Setup dengan Docker

```bash
# Build dan start semua services
docker-compose up -d

# Lihat logs wa-gateway untuk QR code
docker-compose logs -f wa-gateway

# Run migrations & seeders
docker-compose exec api php artisan migrate:fresh --seed

# Cek status
docker-compose ps
```

## 🔐 Default Users

| Role  | Email              | Password |
|-------|--------------------|---------| 
| Admin | admin@example.com  | password |
| Staff | staff@example.com  | password |

## 📱 Testing Flow

### 1. Login sebagai Admin
```
http://localhost:8000/login
Email: admin@example.com
Password: password
```

### 2. Buat Event
- Klik menu **Events**
- Klik **+ Tambah Event**
- Isi nama, venue, tanggal
- Simpan

### 3. Buat Order
- Klik menu **Orders**
- Klik **+ Tambah Order**
- Pilih event, isi customer, phone (format 62xxx), qty
- Simpan

### 4. Mark as PAID
- Di halaman Orders, klik **Mark PAID**
- Sistem akan:
  - Generate tiket sebanyak qty
  - Generate QR Code untuk setiap tiket
  - Enqueue job kirim WhatsApp
- Cek HP, seharusnya menerima gambar QR dengan caption

### 5. Test Check-in (Login Staff)
```
Email: staff@example.com
Password: password
```

- Klik menu **🎫 Check-In**
- Input token (dari QR code, 40 karakter)
- Akan muncul data tiket
- Test:
  - **Approve**: Check-in berhasil
  - **Edit**: Ubah nama/phone/notes
  - **Reject**: Isi alasan, tiket ditolak

## 🛠 API Endpoints (WA Gateway)

### Health Check
```
GET http://localhost:3001/health

Response:
{
  "ok": true,
  "waReady": true,
  "timestamp": "2026-01-23T09:00:00.000Z"
}
```

### Send Image
```
POST http://localhost:3001/send-image
Headers:
  x-api-key: your-secret-api-key
  Content-Type: application/json

Body:
{
  "to": "6281234567890",
  "caption": "Your ticket",
  "filename": "ticket.png",
  "mime": "image/png",
  "base64": "<base64-encoded-image>"
}

Response:
{
  "ok": true,
  "messageId": "..."
}
```

## 📋 Environment Variables

### Laravel (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_ticketing
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

WA_GATEWAY_URL=http://localhost:3001
WA_GATEWAY_KEY=your-secret-api-key
```

### WA Gateway (.env)
```
PORT=3001
API_KEY=your-secret-api-key
```

## 💡 Tips

### Scanner Keyboard Wedge
- Scanner barcode yang mensimulasikan keyboard input
- Pastikan scanner dikonfigurasi untuk menambah Enter di akhir scan
- Halaman check-in sudah auto-focus ke input

### Troubleshooting WhatsApp
1. Jika QR tidak muncul, restart wa-gateway
2. Jika disconnect, tunggu auto-reconnect atau restart
3. Session disimpan di `.wwebjs_auth/`, delete folder untuk reset

### Queue Worker
- Pastikan queue worker berjalan untuk mengirim WA
- `php artisan queue:work` atau via docker-compose

## 📄 License

MIT License
