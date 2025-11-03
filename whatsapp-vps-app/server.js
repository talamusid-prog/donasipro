const express = require('express');
const cors = require('cors');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Logger
const logger = {
    info: (msg) => {
        const timestamp = new Date().toISOString();
        console.log(`[INFO] ${timestamp}: ${msg}`);
        logToFile('info', msg);
    },
    error: (msg) => {
        const timestamp = new Date().toISOString();
        console.error(`[ERROR] ${timestamp}: ${msg}`);
        logToFile('error', msg);
    },
    warn: (msg) => {
        const timestamp = new Date().toISOString();
        console.warn(`[WARN] ${timestamp}: ${msg}`);
        logToFile('warn', msg);
    }
};

// Log to file
function logToFile(level, message) {
    const logDir = path.join(__dirname, 'logs');
    if (!fs.existsSync(logDir)) {
        fs.mkdirSync(logDir, { recursive: true });
    }
    
    const logFile = path.join(logDir, `${level}.log`);
    const timestamp = new Date().toISOString();
    const logEntry = `[${timestamp}] ${message}\n`;
    
    fs.appendFileSync(logFile, logEntry);
}

// Global variables
let client = null;
let isConnected = false;
let deviceInfo = null;
let qrSession = null;
let messageLogs = [];

// WhatsApp Web JS Configuration
const clientConfig = {
    authStrategy: new LocalAuth({
        clientId: 'donasi-apps-vps',
        dataPath: path.join(__dirname, 'sessions')
    }),
    puppeteer: {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--disable-web-security',
            '--disable-features=VizDisplayCompositor',
            '--disable-background-timer-throttling',
            '--disable-backgrounding-occluded-windows',
            '--disable-renderer-backgrounding'
        ]
    }
};

// Initialize WhatsApp Web JS
async function initializeWhatsApp() {
    try {
        logger.info('Initializing WhatsApp Web JS...');
        client = new Client(clientConfig);
        
        client.on('qr', async (qr) => {
            logger.info('QR Code received from WhatsApp Web JS');
            
            // Generate QR code image
            const qrCode = await qrcode.toDataURL(qr);
            qrSession = {
                qr: qrCode,
                sessionId: Date.now().toString(),
                timestamp: Date.now(),
                expiresAt: Date.now() + (2 * 60 * 1000) // 2 minutes
            };
        });

        client.on('ready', () => {
            isConnected = true;
            deviceInfo = {
                platform: 'WhatsApp Web JS',
                browser: 'Puppeteer',
                os: 'VPS',
                connected_at: new Date().toISOString()
            };
            qrSession = null;
            logger.info('WhatsApp Web JS ready!');
        });

        client.on('authenticated', () => {
            logger.info('WhatsApp authenticated');
        });

        client.on('auth_failure', (msg) => {
            logger.error('Auth failure:', msg);
            isConnected = false;
            deviceInfo = null;
        });

        client.on('disconnected', (reason) => {
            logger.info('WhatsApp disconnected:', reason);
            isConnected = false;
            deviceInfo = null;
        });

        client.on('message', async (message) => {
            if (message.fromMe) return;
            
            logger.info('Received message:', {
                from: message.from,
                body: message.body
            });
            
            // Simpan ke log pesan masuk
            messageLogs.unshift({
                timestamp: new Date().toISOString(),
                from: message.from.replace('@c.us', ''),
                message: message.body,
                status: 'received'
            });
            if (messageLogs.length > 100) messageLogs = messageLogs.slice(0, 100);
        });

        await client.initialize();
        logger.info('WhatsApp Web JS initialized successfully');
        
    } catch (error) {
        logger.error('Error initializing WhatsApp Web JS:', error);
    }
}

// Health check
app.get('/health', (req, res) => {
    res.json({ 
        status: 'ok', 
        timestamp: new Date().toISOString(),
        whatsapp: {
            connected: isConnected,
            hasSession: !!qrSession,
            client: !!client,
            method: 'WhatsApp Web JS (VPS)'
        }
    });
});

// Get WhatsApp status
app.get('/whatsapp/status', (req, res) => {
    res.json({
        connected: isConnected,
        status: isConnected ? 'connected' : 'disconnected',
        message: isConnected ? 'Device terhubung' : 'Device tidak terhubung',
        device_info: deviceInfo,
        method: 'WhatsApp Web JS (VPS)'
    });
});

// Generate QR Code
app.get('/whatsapp/qr', async (req, res) => {
    try {
        if (isConnected) {
            return res.json({
                success: false,
                message: 'Device sudah terhubung'
            });
        }

        if (!client) {
            await initializeWhatsApp();
        }

        // Wait for QR code
        let attempts = 0;
        while (!qrSession && attempts < 30) {
            await new Promise(resolve => setTimeout(resolve, 1000));
            attempts++;
        }

        if (qrSession) {
            res.json({
                success: true,
                qr: qrSession.qr,
                session_id: qrSession.sessionId,
                expires_at: qrSession.expiresAt,
                status: 'ready',
                message: 'QR Code siap untuk scan'
            });
        } else {
            res.json({
                success: false,
                message: 'Gagal generate QR code'
            });
        }

    } catch (error) {
        logger.error('Error generating QR:', error);
        res.status(500).json({
            success: false,
            message: 'Terjadi kesalahan saat generate QR'
        });
    }
});

// Check QR status
app.get('/whatsapp/qr-status', (req, res) => {
    const { session_id } = req.query;
    
    if (!session_id) {
        return res.status(400).json({
            success: false,
            message: 'Session ID diperlukan'
        });
    }

    if (isConnected) {
        res.json({
            status: 'connected',
            message: 'Device berhasil terhubung'
        });
    } else if (qrSession && qrSession.sessionId === session_id) {
        // Check if QR expired
        if (Date.now() > qrSession.expiresAt) {
            qrSession = null;
            res.json({
                status: 'expired',
                message: 'QR Code sudah expired'
            });
        } else {
            res.json({
                status: 'pending',
                message: 'Menunggu scan QR Code'
            });
        }
    } else {
        res.json({
            status: 'not_found',
            message: 'Session tidak ditemukan'
        });
    }
});

// Send message
app.post('/whatsapp/send', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                message: 'Phone dan message diperlukan'
            });
        }

        if (!isConnected || !client) {
            return res.status(400).json({
                success: false,
                message: 'Device belum terhubung'
            });
        }

        // Format phone number
        let formattedPhone = phone.replace(/\D/g, '');
        if (!formattedPhone.endsWith('@c.us')) {
            formattedPhone += '@c.us';
        }

        // Send message via WhatsApp Web JS
        const chat = await client.getChatById(formattedPhone);
        await chat.sendMessage(message);

        logger.info(`Message sent to ${formattedPhone}: ${message}`);
        
        // Simpan ke log pesan keluar
        messageLogs.unshift({
            timestamp: new Date().toISOString(),
            to: formattedPhone.replace('@c.us', ''),
            message: message,
            status: 'sent'
        });
        if (messageLogs.length > 100) messageLogs = messageLogs.slice(0, 100);

        res.json({
            success: true,
            message: 'Pesan berhasil dikirim',
            data: {
                to: formattedPhone,
                message: message,
                timestamp: new Date().toISOString()
            }
        });

    } catch (error) {
        logger.error('Error sending message:', error);
        res.status(500).json({
            success: false,
            message: 'Terjadi kesalahan saat mengirim pesan'
        });
    }
});

// Disconnect device
app.post('/whatsapp/disconnect', async (req, res) => {
    try {
        if (client) {
            await client.destroy();
            client = null;
        }
        
        isConnected = false;
        deviceInfo = null;
        qrSession = null;

        logger.info('WhatsApp disconnected');

        res.json({
            success: true,
            message: 'Device berhasil diputuskan'
        });
    } catch (error) {
        logger.error('Error disconnecting:', error);
        res.status(500).json({
            success: false,
            message: 'Terjadi kesalahan saat memutuskan koneksi'
        });
    }
});

// Get message logs
app.get('/whatsapp/logs', (req, res) => {
    const limit = parseInt(req.query.limit) || 20;
    res.json({
        success: true,
        data: messageLogs.slice(0, limit)
    });
});

// Start server
app.listen(PORT, () => {
    logger.info(`WhatsApp VPS server running on port ${PORT}`);
    logger.info(`Health check: http://localhost:${PORT}/health`);
    logger.info(`WhatsApp status: http://localhost:${PORT}/whatsapp/status`);
    
    // Initialize WhatsApp on startup
    initializeWhatsApp();
}); 