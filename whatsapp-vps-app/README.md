# WhatsApp VPS Application

Aplikasi WhatsApp khusus untuk dijalankan di VPS dan terhubung ke aplikasi donasi via API.

## Struktur Folder

```
whatsapp-vps-app/
├── server.js              # Server utama
├── package.json           # Dependencies
├── ecosystem.config.js    # PM2 configuration
├── .env.example          # Environment variables example
├── .env                  # Environment variables (buat sendiri)
├── logs/                 # Log files
├── sessions/             # WhatsApp sessions
└── README.md            # Dokumentasi ini
```

## Fitur

- ✅ WhatsApp Web JS dengan Puppeteer
- ✅ QR Code generation yang valid
- ✅ API untuk kirim pesan
- ✅ Webhook untuk terima pesan
- ✅ Log pesan masuk/keluar
- ✅ PM2 untuk process management
- ✅ Environment variables
- ✅ CORS untuk cross-origin requests

## Setup di VPS

### 1. Install Dependencies
```bash
# Install Node.js (jika belum)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install dependencies
npm install
```

### 2. Setup Environment
```bash
# Copy environment example
cp .env.example .env

# Edit environment variables
nano .env
```

### 3. Jalankan dengan PM2
```bash
# Install PM2 globally
npm install -g pm2

# Start aplikasi
npm run pm2

# Cek status
pm2 status

# Cek logs
pm2 logs whatsapp-vps-app
```

## API Endpoints

### Health Check
```
GET /health
```

### WhatsApp Status
```
GET /whatsapp/status
```

### Generate QR Code
```
GET /whatsapp/qr
```

### Check QR Status
```
GET /whatsapp/qr-status?session_id=xxx
```

### Send Message
```
POST /whatsapp/send
{
  "phone": "628123456789",
  "message": "Test pesan"
}
```

### Get Message Logs
```
GET /whatsapp/logs?limit=20
```

### Disconnect Device
```
POST /whatsapp/disconnect
```

## Konfigurasi Laravel

Update `config/whatsapp.php` di aplikasi donasi:

```php
'web_api' => [
    'node_url' => env('WHATSAPP_NODE_URL', 'http://your-vps-ip:3001'),
    'enabled' => true,
],
```

Update `.env` di aplikasi donasi:

```env
WHATSAPP_NODE_URL=http://your-vps-ip:3001
```

## Security

- Gunakan firewall untuk membatasi akses
- Gunakan HTTPS untuk production
- Monitor logs secara berkala
- Backup sessions secara rutin

## Troubleshooting

### Jika QR code tidak muncul:
```bash
# Cek logs
pm2 logs whatsapp-vps-app

# Restart aplikasi
pm2 restart whatsapp-vps-app
```

### Jika Puppeteer error:
```bash
# Install dependencies sistem
sudo apt-get update
sudo apt-get install -y \
    gconf-service \
    libasound2 \
    libatk1.0-0 \
    libc6 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgcc1 \
    libgconf-2-4 \
    libgdk-pixbuf2.0-0 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
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
    ca-certificates \
    fonts-liberation \
    libappindicator1 \
    libnss3 \
    lsb-release \
    xdg-utils \
    wget
```

## Monitoring

```bash
# Cek status aplikasi
pm2 status

# Cek penggunaan resource
pm2 monit

# Cek logs real-time
pm2 logs whatsapp-vps-app --lines 100

# Restart aplikasi
pm2 restart whatsapp-vps-app
``` 