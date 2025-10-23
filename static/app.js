// SUNSWEEP Robot Dashboard JavaScript
const API_BASE_URL = window.location.origin;

// Auto-refresh interval (5 seconds)
let refreshInterval = null;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('SUNSWEEP Dashboard initialized');
    loadStatus();
    loadConfig();
    loadHistory();
    
    // Start auto-refresh
    refreshInterval = setInterval(loadStatus, 5000);
});

// Load robot status
async function loadStatus() {
    try {
        const response = await fetch(`${API_BASE_URL}/api/status`);
        const result = await response.json();
        
        if (result.success) {
            updateStatusDisplay(result.data);
        }
    } catch (error) {
        console.error('Error loading status:', error);
    }
}

// Update status display
function updateStatusDisplay(data) {
    // Update status text
    const statusElement = document.getElementById('robot-status');
    statusElement.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
    statusElement.className = 'value status-' + data.status;
    
    // Update battery
    const batteryLevel = document.getElementById('battery-level');
    const batteryFill = document.getElementById('battery-fill');
    batteryLevel.textContent = data.battery + '%';
    batteryFill.style.width = data.battery + '%';
    
    // Update battery color based on level
    batteryFill.classList.remove('low', 'critical');
    if (data.battery <= 10) {
        batteryFill.classList.add('critical');
    } else if (data.battery <= 20) {
        batteryFill.classList.add('low');
    }
    
    // Update temperature
    document.getElementById('temperature').textContent = data.temperature + '°C';
    
    // Update position
    document.getElementById('position').textContent = 
        `X: ${data.position.x}, Y: ${data.position.y}`;
    
    // Update cleaning mode
    document.getElementById('cleaning-mode').textContent = 
        data.cleaning_mode.charAt(0).toUpperCase() + data.cleaning_mode.slice(1);
    
    // Update last update time
    const lastUpdate = new Date(data.last_update);
    document.getElementById('last-update').textContent = 
        lastUpdate.toLocaleTimeString();
    
    // Update error display
    const errorDisplay = document.getElementById('error-display');
    const errorMessage = document.getElementById('error-message');
    if (data.error) {
        errorDisplay.style.display = 'block';
        errorMessage.textContent = data.error;
    } else {
        errorDisplay.style.display = 'none';
    }
}

// Send command to robot
async function sendCommand(command) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/command`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ command: command })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Command sent successfully: ' + command, 'success');
            // Reload status after a short delay
            setTimeout(loadStatus, 1000);
        } else {
            showNotification('Error: ' + result.error, 'error');
        }
    } catch (error) {
        console.error('Error sending command:', error);
        showNotification('Failed to send command', 'error');
    }
}

// Load notification config
async function loadConfig() {
    try {
        const response = await fetch(`${API_BASE_URL}/api/config`);
        const result = await response.json();
        
        if (result.success) {
            updateConfigDisplay(result.data);
        }
    } catch (error) {
        console.error('Error loading config:', error);
    }
}

// Update config display
function updateConfigDisplay(config) {
    document.getElementById('email-enabled').checked = config.email_enabled;
    document.getElementById('sms-enabled').checked = config.sms_enabled;
    document.getElementById('email-on-error').checked = config.email_on_error;
    document.getElementById('sms-on-error').checked = config.sms_on_error;
    document.getElementById('email-on-low-battery').checked = config.email_on_low_battery;
    document.getElementById('sms-on-low-battery').checked = config.sms_on_low_battery;
    document.getElementById('low-battery-threshold').value = config.low_battery_threshold;
}

// Update notification config
async function updateConfig() {
    const config = {
        email_enabled: document.getElementById('email-enabled').checked,
        sms_enabled: document.getElementById('sms-enabled').checked,
        email_on_error: document.getElementById('email-on-error').checked,
        sms_on_error: document.getElementById('sms-on-error').checked,
        email_on_low_battery: document.getElementById('email-on-low-battery').checked,
        sms_on_low_battery: document.getElementById('sms-on-low-battery').checked,
        low_battery_threshold: parseInt(document.getElementById('low-battery-threshold').value)
    };
    
    try {
        const response = await fetch(`${API_BASE_URL}/api/config`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(config)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Configuration updated', 'success');
        } else {
            showNotification('Error updating configuration', 'error');
        }
    } catch (error) {
        console.error('Error updating config:', error);
        showNotification('Failed to update configuration', 'error');
    }
}

// Test notification
async function testNotification(type) {
    try {
        const response = await fetch(`${API_BASE_URL}/api/test-notification`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ type: type })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
        } else {
            showNotification('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error testing notification:', error);
        showNotification('Failed to send test notification', 'error');
    }
}

// Load status history
async function loadHistory() {
    try {
        const response = await fetch(`${API_BASE_URL}/api/history?limit=20`);
        const result = await response.json();
        
        if (result.success) {
            updateHistoryDisplay(result.data);
        }
    } catch (error) {
        console.error('Error loading history:', error);
    }
}

// Update history display
function updateHistoryDisplay(history) {
    const container = document.getElementById('history-container');
    
    if (history.length === 0) {
        container.innerHTML = '<p class="no-data">No history data available</p>';
        return;
    }
    
    container.innerHTML = '';
    
    // Reverse to show newest first
    history.reverse().forEach(item => {
        const div = document.createElement('div');
        div.className = 'history-item' + (item.error ? ' error' : '');
        
        const timestamp = new Date(item.timestamp);
        
        div.innerHTML = `
            <div class="timestamp">${timestamp.toLocaleString()}</div>
            <div class="details">
                <strong>Status:</strong> ${item.status} | 
                <strong>Battery:</strong> ${item.battery}%
                ${item.error ? `<br><strong>Error:</strong> ${item.error}` : ''}
            </div>
        `;
        
        container.appendChild(div);
    });
}

// Show notification (simple toast-style notification)
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4caf50' : '#f44336'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
