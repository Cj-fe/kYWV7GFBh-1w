// server.js - Node.js backend
const express = require('express');
const { exec } = require('child_process');
const os = require('os');
const path = require('path');
const app = express();
const port = 3000;

// Serve static files
app.use(express.static(path.join(__dirname, 'public')));

// Get local IP address
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const devName in interfaces) {
        const iface = interfaces[devName];
        for (let i = 0; i < iface.length; i++) {
            const alias = iface[i];
            if (alias.family === 'IPv4' && !alias.internal) {
                return alias.address;
            }
        }
    }
    return '127.0.0.1';
}

// Get network base (e.g., 192.168.1)
function getNetworkBase(ip) {
    return ip.split('.').slice(0, 3).join('.');
}

// API endpoint to scan network
app.get('/api/scan', (req, res) => {
    const localIP = getLocalIP();
    const networkBase = req.query.networkBase || getNetworkBase(localIP);
    
    console.log(`Scanning network: ${networkBase}.0/24`);
    
    // Different commands for different platforms
    let scanCommand;
    if (process.platform === 'win32') {
        // Windows
        scanCommand = 'arp -a';
    } else {
        // Linux/Mac
        scanCommand = `ping -c 1 -b ${networkBase}.255 >/dev/null 2>&1 && arp -a`;
    }
    
    exec(scanCommand, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error executing scan: ${error}`);
            return res.status(500).json({ error: 'Failed to scan network', details: error.message });
        }
        
        const devices = parseArpOutput(stdout, networkBase);
        res.json({ devices, networkInfo: { localIP, networkBase } });
    });
});

// Parse ARP table output
function parseArpOutput(output, networkBase) {
    const devices = [];
    const lines = output.split('\n');
    
    // Different parsing for different platforms
    if (process.platform === 'win32') {
        // Windows ARP format
        for (const line of lines) {
            if (line.includes(networkBase)) {
                const parts = line.trim().split(/\s+/);
                if (parts.length >= 2) {
                    const ip = parts[0];
                    const mac = parts[1].replace(/-/g, ':').toUpperCase();
                    if (mac !== '00:00:00:00:00:00' && mac !== 'FF:FF:FF:FF:FF:FF') {
                        devices.push({
                            ip,
                            mac,
                            name: getDeviceName(ip),
                            status: 'online'
                        });
                    }
                }
            }
        }
    } else {
        // Linux/Mac ARP format
        for (const line of lines) {
            if (line.includes(networkBase)) {
                const match = line.match(/\(([^)]+)\) at ([a-fA-F0-9:]+)/);
                if (match && match.length >= 3) {
                    const ip = match[1];
                    const mac = match[2].toUpperCase();
                    if (mac !== '00:00:00:00:00:00' && mac !== 'FF:FF:FF:FF:FF:FF') {
                        devices.push({
                            ip,
                            mac,
                            name: getDeviceName(ip),
                            status: 'online'
                        });
                    }
                }
            }
        }
    }
    
    return devices;
}

// Try to get hostname (will be mostly empty on regular networks due to privacy settings)
function getDeviceName(ip) {
    // In a real implementation, you could try DNS lookup
    // For now, return empty as most devices don't advertise names
    return '';
}

// Start server
app.listen(port, () => {
    const localIP = getLocalIP();
    console.log(`Network scanner running at http://${localIP}:${port}`);
});
