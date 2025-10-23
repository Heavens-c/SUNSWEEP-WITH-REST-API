# SUNSWEEP Robot Dashboard with REST API

This is a capstone thesis project for Our Lady of Fatima University - A web-based dashboard for monitoring and controlling a SUNSWEEP robot using REST API, with email and SMS notification capabilities.

## Features

- **Real-time Robot Monitoring**: View robot status, battery level, position, temperature, and cleaning mode
- **Remote Control**: Send commands to the robot (start, stop, pause, return home)
- **Notification System**: Email and SMS alerts for errors and low battery
- **Status History**: Track robot activity over time
- **Web Dashboard**: Beautiful, responsive interface for monitoring and control
- **REST API**: Complete API for robot-to-server communication

## Project Structure

```
SUNSWEEP-WITH-REST-API/
├── app.py                    # Main Flask server with REST API endpoints
├── notification_service.py   # Email and SMS notification service
├── robot_simulator.py        # Robot simulator for testing
├── requirements.txt          # Python dependencies
├── .env.example             # Example environment configuration
├── static/
│   ├── index.html           # Web dashboard interface
│   ├── styles.css           # Dashboard styling
│   └── app.js               # Dashboard JavaScript
└── README.md                # This file
```

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Heavens-c/SUNSWEEP-WITH-REST-API.git
   cd SUNSWEEP-WITH-REST-API
   ```

2. **Install Python dependencies**
   ```bash
   pip install -r requirements.txt
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` and add your configuration:
   - Email settings (SMTP server, username, password)
   - Twilio settings for SMS (account SID, auth token, phone numbers)
   - Server settings (host, port, debug mode)

## Configuration

### Email Setup (Gmail Example)

1. Enable 2-factor authentication on your Gmail account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Update `.env`:
   ```
   SMTP_SERVER=smtp.gmail.com
   SMTP_PORT=587
   SMTP_USERNAME=your_email@gmail.com
   SMTP_PASSWORD=your_app_password
   NOTIFICATION_EMAIL=recipient@example.com
   ```

### SMS Setup (Twilio)

1. Sign up for Twilio: https://www.twilio.com/
2. Get your Account SID and Auth Token from the Twilio Console
3. Get a Twilio phone number
4. Update `.env`:
   ```
   TWILIO_ACCOUNT_SID=your_account_sid
   TWILIO_AUTH_TOKEN=your_auth_token
   TWILIO_PHONE_NUMBER=+1234567890
   NOTIFICATION_PHONE=+1234567890
   ```

## Usage

### Starting the Server

```bash
python app.py
```

The server will start on `http://localhost:5000` (or the configured host/port).

### Accessing the Dashboard

Open your web browser and navigate to:
```
http://localhost:5000
```

### Testing with Robot Simulator

Run the robot simulator in a separate terminal:
```bash
python robot_simulator.py
```

This will simulate robot activity and send status updates to the server.

## REST API Endpoints

### Get Robot Status
```
GET /api/status
```
Returns current robot status, battery, position, temperature, etc.

### Update Robot Status
```
POST /api/status
Content-Type: application/json

{
  "status": "cleaning",
  "battery": 85,
  "position": {"x": 10, "y": 20},
  "temperature": 28,
  "cleaning_mode": "auto",
  "error": null
}
```

### Send Command to Robot
```
POST /api/command
Content-Type: application/json

{
  "command": "start"
}
```

Valid commands: `start`, `stop`, `pause`, `resume`, `return_home`, `auto`, `spot`, `edge`

### Get Notification Configuration
```
GET /api/config
```

### Update Notification Configuration
```
POST /api/config
Content-Type: application/json

{
  "email_enabled": true,
  "sms_enabled": false,
  "email_on_error": true,
  "email_on_low_battery": true,
  "low_battery_threshold": 20
}
```

### Test Notifications
```
POST /api/test-notification
Content-Type: application/json

{
  "type": "email"  // or "sms"
}
```

### Get Status History
```
GET /api/history?limit=50
```

## Robot Integration

To integrate with a real robot, have the robot periodically send status updates:

```python
import requests
import json

robot_status = {
    "status": "cleaning",
    "battery": 75,
    "position": {"x": 5, "y": 10},
    "temperature": 30,
    "cleaning_mode": "auto",
    "error": None
}

response = requests.post(
    'http://your-server:5000/api/status',
    headers={'Content-Type': 'application/json'},
    data=json.dumps(robot_status)
)
```

## Dashboard Features

- **Real-time Updates**: Status refreshes every 5 seconds
- **Control Buttons**: Start, pause, stop, return home
- **Cleaning Modes**: Auto, spot, edge cleaning modes
- **Notification Settings**: Configure email/SMS alerts
- **Status History**: View recent robot activity
- **Battery Indicator**: Visual battery level with color coding
- **Error Alerts**: Prominent display of error conditions

## Notification Triggers

The system automatically sends notifications when:

1. **Error Detected**: When the robot encounters an error
2. **Low Battery**: When battery falls below the configured threshold
3. **Manual Test**: Using the "Test Email" or "Test SMS" buttons

## Security Notes

- Never commit the `.env` file to version control
- Use strong passwords and keep API keys secure
- Consider implementing authentication for production use
- Use HTTPS in production environments

## Development

### Adding New Features

1. API endpoints: Add to `app.py`
2. Notification methods: Modify `notification_service.py`
3. Dashboard UI: Update `static/index.html`, `static/styles.css`, `static/app.js`

### Testing

Use the provided `robot_simulator.py` to test the system without a physical robot.

## Troubleshooting

### Email not sending
- Check SMTP credentials in `.env`
- Verify firewall allows SMTP connections
- For Gmail, ensure App Password is used (not regular password)

### SMS not sending
- Verify Twilio credentials
- Check phone number format (include country code)
- Ensure Twilio account has sufficient balance

### Dashboard not loading
- Check if Flask server is running
- Verify correct URL (http://localhost:5000)
- Check browser console for JavaScript errors

## License

This project is part of a capstone thesis for Our Lady of Fatima University.

## Contributors

Our Lady of Fatima University - Capstone Thesis Team

## Support

For issues and questions, please open an issue on GitHub.
