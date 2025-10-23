# Quick Start Guide

## Get Started in 3 Steps

### 1. Install Dependencies
```bash
pip install -r requirements.txt
```

### 2. Start the Server
```bash
python app.py
```

### 3. Open the Dashboard
Open your browser and go to: http://localhost:5000

## Test Without a Robot

Run the robot simulator in a separate terminal:
```bash
python robot_simulator.py
```

This will simulate robot activity and send status updates to the server.

## Enable Notifications (Optional)

1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your credentials:
   - For email: Add SMTP settings (Gmail, etc.)
   - For SMS: Add Twilio credentials

3. In the dashboard, enable notifications and click "Test Email" or "Test SMS"

## API Usage Example

Send a status update from your robot:
```python
import requests
import json

status = {
    "status": "cleaning",
    "battery": 75,
    "position": {"x": 5, "y": 10},
    "temperature": 30,
    "cleaning_mode": "auto"
}

requests.post(
    'http://localhost:5000/api/status',
    headers={'Content-Type': 'application/json'},
    data=json.dumps(status)
)
```

## Default Settings

- Server: http://0.0.0.0:5000
- Auto-refresh: Every 5 seconds
- Low battery threshold: 20%
- History limit: 100 entries

## Need Help?

See the full [README.md](README.md) for detailed documentation.
