#!/bin/bash

# WhatsApp VPS Application Setup Script

echo "=== WhatsApp VPS Application Setup ==="

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "❌ Jangan jalankan script ini sebagai root"
    exit 1
fi

# Update system
echo "📦 Updating system packages..."
sudo apt-get update

# Install Node.js if not installed
if ! command -v node &> /dev/null; then
    echo "📦 Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
else
    echo "✅ Node.js already installed"
fi

# Install PM2 globally
echo "📦 Installing PM2..."
sudo npm install -g pm2

# Install system dependencies for Puppeteer
echo "📦 Installing system dependencies for Puppeteer..."
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

# Install npm dependencies
echo "📦 Installing npm dependencies..."
npm install

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p logs
mkdir -p sessions

# Copy environment file
if [ ! -f .env ]; then
    echo "📄 Creating .env file..."
    cp env.example .env
    echo "✅ Please edit .env file with your configuration"
else
    echo "✅ .env file already exists"
fi

# Set permissions
echo "🔐 Setting permissions..."
chmod 755 logs
chmod 755 sessions

echo ""
echo "=== Setup Complete ==="
echo ""
echo "Next steps:"
echo "1. Edit .env file with your configuration"
echo "2. Start the application: npm run pm2"
echo "3. Check status: pm2 status"
echo "4. View logs: pm2 logs whatsapp-vps-app"
echo ""
echo "For more information, see README.md" 