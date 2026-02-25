require('dotenv').config();

const express = require('express');
const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3001;
const API_KEY = process.env.API_KEY || '';

// Middleware
app.use(express.json({ limit: '25mb' }));
app.use(express.urlencoded({ limit: '25mb', extended: true }));

// Multi-Session State
// Map<sessionId, Client>
const sessions = new Map();
// Map<sessionId, { ready: boolean, qr: string|null, info: any }>
const sessionData = new Map();

// API Key Authentication Middleware
const authMiddleware = (req, res, next) => {
    const apiKey = req.headers['x-api-key'];
    
    if (!API_KEY) {
        return next();
    }
    
    if (!apiKey || apiKey !== API_KEY) {
        return res.status(401).json({ 
            ok: false, 
            error: 'Unauthorized - Invalid API key' 
        });
    }
    
    next();
};

// Initialize Session
const initSession = async (sessionId) => {
    if (sessions.has(sessionId)) {
        return sessions.get(sessionId);
    }

    console.log(`Initializing session: ${sessionId}`);
    
    // Initialize session data
    sessionData.set(sessionId, {
        ready: false,
        qr: null,
        info: null
    });

    // Auto-detect Chromium path (Linux VPS)
    const getChromiumPath = () => {
        const candidates = [
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            '/snap/bin/chromium',
        ];
        for (const p of candidates) {
            if (fs.existsSync(p)) return p;
        }
        return null; // pakai bundled puppeteer
    };

    const chromiumPath = getChromiumPath() || process.env.PUPPETEER_EXECUTABLE_PATH;
    const puppeteerOptions = {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu',
            '--single-process',
            '--disable-extensions'
        ]
    };
    if (chromiumPath) {
        puppeteerOptions.executablePath = chromiumPath;
        console.log(`[${sessionId}] Using Chromium: ${chromiumPath}`);
    } else {
        console.log(`[${sessionId}] WARNING: No system Chromium found, relying on bundled Puppeteer...`);
    }

    const client = new Client({
        authStrategy: new LocalAuth({
            clientId: sessionId,
            dataPath: './.wwebjs_auth'
        }),
        puppeteer: puppeteerOptions,
        webVersionCache: {
            type: 'remote',
            remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/refs/heads/main/html/2.3000.1031490220-alpha.html',
        }
    });

    // Event Listeners
    client.on('qr', (qr) => {
        console.log(`[${sessionId}] QR Code Received`);
        const data = sessionData.get(sessionId);
        if (data) {
            data.qr = qr;
            data.ready = false;
        }
    });

    client.on('ready', () => {
        console.log(`[${sessionId}] Client is ready!`);
        const data = sessionData.get(sessionId);
        if (data) {
            data.ready = true;
            data.qr = null;
            data.info = client.info;
        }
    });

    client.on('authenticated', () => {
        console.log(`[${sessionId}] Authenticated`);
        const data = sessionData.get(sessionId);
        if (data) data.qr = null;
    });

    client.on('auth_failure', (msg) => {
        console.error(`[${sessionId}] Auth failure: ${msg}`);
        const data = sessionData.get(sessionId);
        if (data) {
            data.ready = false;
            data.qr = null;
        }
    });

    client.on('disconnected', (reason) => {
        console.log(`[${sessionId}] Disconnected: ${reason}`);
        const data = sessionData.get(sessionId);
        if (data) {
            data.ready = false;
            data.qr = null;
        }
        // Optional: Auto reconnect or destroy
        // client.destroy();
        // sessions.delete(sessionId);
    });

    try {
        await client.initialize();
        sessions.set(sessionId, client);
        return client;
    } catch (err) {
        console.error(`[${sessionId}] Init Error:`, err);
        throw err;
    }
};

// Routes

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        ok: true,
        sessions: sessions.size,
        timestamp: new Date().toISOString()
    });
});

// Start Session
app.post('/session/start', authMiddleware, async (req, res) => {
    try {
        const { sessionId } = req.body;
        if (!sessionId) return res.status(400).json({ error: 'sessionId required' });

        if (sessions.has(sessionId)) {
            return res.json({ message: 'Session already active', sessionId });
        }

        await initSession(sessionId);
        res.json({ message: 'Session initialization started', sessionId });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get Session Status
app.get('/session/:sessionId/status', authMiddleware, (req, res) => {
    const { sessionId } = req.params;
    const data = sessionData.get(sessionId);
    
    if (!data) {
        return res.status(404).json({ 
            connected: false, 
            message: 'Session not found. Please start session first.' 
        });
    }

    // Get phone info if ready
    let phoneInfo = null;
    if (data.ready && sessions.has(sessionId)) {
         const client = sessions.get(sessionId);
         phoneInfo = client.info ? {
             wid: client.info.wid,
             pushname: client.info.pushname,
             platform: client.info.platform
         } : null;
    }

    res.json({
        connected: data.ready,
        qr: data.qr,
        info: phoneInfo
    });
});

// Logout Session
app.post('/session/:sessionId/logout', authMiddleware, async (req, res) => {
    const { sessionId } = req.params;
    const client = sessions.get(sessionId);
    
    if (!client) return res.status(404).json({ error: 'Session not found' });

    try {
        await client.logout();
        // Cleanup will happen in disconnected event, but we can force it
        // Remove from memory
        sessions.delete(sessionId);
        sessionData.delete(sessionId);
        
        // Also clean up auth folder?
        // const authPath = path.join('.wwebjs_auth', `session-${sessionId}`);
        // fs.rmSync(authPath, { recursive: true, force: true });

        res.json({ message: 'Logged out successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Delete Session (Stop)
app.delete('/session/:sessionId', authMiddleware, async (req, res) => {
    const { sessionId } = req.params;
    const client = sessions.get(sessionId);
    
    if (client) {
        await client.destroy();
        sessions.delete(sessionId);
        sessionData.delete(sessionId);
    }
    
    res.json({ message: 'Session stopped and removed' });
});

// Send Image
app.post('/session/:sessionId/send-image', authMiddleware, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, caption, filename, mime, base64 } = req.body;

        const client = sessions.get(sessionId);
        if (!client) return res.status(404).json({ error: 'Session not found or not active' });
        
        const data = sessionData.get(sessionId);
        if (!data || !data.ready) return res.status(503).json({ error: 'Session not ready' });

        // Logic
        if (!to || !base64) return res.status(400).json({ error: 'Missing parameters' });
        
        const chatId = `${to}@c.us`;
        const media = new MessageMedia(mime || 'image/png', base64, filename || 'image.png');

        // Retry logic (markedUnread bug)
        let attempts = 0;
        let sent = false;
        let lastError = null;
        
        while (attempts < 3) {
            try {
                await client.sendMessage(chatId, media, { caption: caption || '' });
                sent = true;
                break;
            } catch (err) {
                lastError = err;
                if (err.message && err.message.includes('markedUnread')) {
                    attempts++;
                    await new Promise(r => setTimeout(r, 1000));
                     if (attempts >= 3) sent = true; // Assume success if only markedUnread
                } else {
                    break;
                }
            }
        }

        if (sent) {
            console.log(`[${sessionId}] Image sent to ${to}`);
            res.json({ ok: true });
        } else {
            console.error(`[${sessionId}] Send Error:`, lastError);
            res.status(500).json({ ok: false, error: lastError?.message });
        }

    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Send Text
app.post('/session/:sessionId/send-text', authMiddleware, async (req, res) => {
    try {
        const { sessionId } = req.params;
        const { to, message } = req.body;

        const client = sessions.get(sessionId);
        if (!client) return res.status(404).json({ error: 'Session not found' });
        
        const data = sessionData.get(sessionId);
        if (!data || !data.ready) return res.status(503).json({ error: 'Session not ready' });

        const chatId = `${to}@c.us`;
        await client.sendMessage(chatId, message);
        
        console.log(`[${sessionId}] Text sent to ${to}`);
        res.json({ ok: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Restore sessions on startup
// Scan .wwebjs_auth folder to find existing sessions
const restoreSessions = async () => {
    const authDir = './.wwebjs_auth';
    if (!fs.existsSync(authDir)) return;

    const files = fs.readdirSync(authDir);
    for (const file of files) {
        if (file.startsWith('session-')) {
            const sessionId = file.replace('session-', '');
            console.log(`Restoring session: ${sessionId}`);
            try {
                await initSession(sessionId);
            } catch (err) {
                console.error(`[${sessionId}] Restore failed, skipping:`, err.message);
                // Hapus session rusak agar tidak restore lagi
                sessions.delete(sessionId);
                sessionData.delete(sessionId);
            }
        }
    }
};

// Prevent server crash dari unhandled promise rejection
process.on('unhandledRejection', (reason, promise) => {
    console.error('[WARN] Unhandled Rejection:', reason?.message || reason);
});

// Start server
app.listen(PORT, async () => {
    console.log(`\n🚀 WA Multi-Session Gateway running on http://localhost:${PORT}`);
    console.log(`   API Key: ${API_KEY ? 'CONFIGURED' : 'NOT SET'}`);
    
    await restoreSessions();
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\nShutting down...');
    for (const [id, client] of sessions) {
        await client.destroy();
    }
    process.exit(0);
});
