# SUNSWEEP REST API Documentation

Base URL: `http://localhost:5000/api`

## Endpoints

### 1. Get Robot Status

**Endpoint:** `GET /api/status`

**Description:** Retrieve current robot status, battery level, position, and other information.

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "cleaning",
    "battery": 85,
    "position": {"x": 10, "y": 20},
    "temperature": 28,
    "cleaning_mode": "auto",
    "error": null,
    "last_update": "2025-10-23T16:13:47.082142",
    "history": [...]
  }
}
```

**Status Values:**
- `idle` - Robot is idle
- `cleaning` - Robot is cleaning
- `charging` - Robot is charging
- `returning_home` - Robot is returning to base
- `error` - Robot has encountered an error

---

### 2. Update Robot Status

**Endpoint:** `POST /api/status`

**Description:** Update robot status from the robot device.

**Request Body:**
```json
{
  "status": "cleaning",
  "battery": 85,
  "position": {"x": 10, "y": 20},
  "temperature": 28,
  "cleaning_mode": "auto",
  "error": null
}
```

**Parameters:**
- `status` (string, optional) - Current robot status
- `battery` (number, optional) - Battery level (0-100)
- `position` (object, optional) - Robot position with x and y coordinates
- `temperature` (number, optional) - Robot temperature in Celsius
- `cleaning_mode` (string, optional) - Current cleaning mode
- `error` (string, optional) - Error message if any

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "cleaning",
    "battery": 85,
    ...
  }
}
```

---

### 3. Send Command to Robot

**Endpoint:** `POST /api/command`

**Description:** Send a command to the robot.

**Request Body:**
```json
{
  "command": "start"
}
```

**Valid Commands:**
- `start` - Start cleaning
- `stop` - Stop cleaning
- `pause` - Pause cleaning
- `resume` - Resume cleaning
- `return_home` - Return to charging base
- `auto` - Switch to auto cleaning mode
- `spot` - Switch to spot cleaning mode
- `edge` - Switch to edge cleaning mode

**Response:**
```json
{
  "success": true,
  "message": "Command start queued successfully"
}
```

---

### 4. Get Notification Configuration

**Endpoint:** `GET /api/config`

**Description:** Get current notification settings.

**Response:**
```json
{
  "success": true,
  "data": {
    "email_enabled": false,
    "sms_enabled": false,
    "email_on_error": true,
    "email_on_low_battery": true,
    "sms_on_error": true,
    "sms_on_low_battery": false,
    "low_battery_threshold": 20
  }
}
```

---

### 5. Update Notification Configuration

**Endpoint:** `POST /api/config`

**Description:** Update notification settings.

**Request Body:**
```json
{
  "email_enabled": true,
  "sms_enabled": false,
  "email_on_error": true,
  "email_on_low_battery": true,
  "sms_on_error": false,
  "sms_on_low_battery": false,
  "low_battery_threshold": 20
}
```

**Parameters:**
- `email_enabled` (boolean) - Enable/disable email notifications
- `sms_enabled` (boolean) - Enable/disable SMS notifications
- `email_on_error` (boolean) - Send email when error occurs
- `email_on_low_battery` (boolean) - Send email when battery is low
- `sms_on_error` (boolean) - Send SMS when error occurs
- `sms_on_low_battery` (boolean) - Send SMS when battery is low
- `low_battery_threshold` (number) - Battery level threshold (0-100)

**Response:**
```json
{
  "success": true,
  "data": {
    "email_enabled": true,
    ...
  }
}
```

---

### 6. Test Notification

**Endpoint:** `POST /api/test-notification`

**Description:** Send a test notification.

**Request Body:**
```json
{
  "type": "email"
}
```

**Parameters:**
- `type` (string) - Notification type: `email` or `sms`

**Response:**
```json
{
  "success": true,
  "message": "Test email sent successfully"
}
```

---

### 7. Get Status History

**Endpoint:** `GET /api/history?limit=50`

**Description:** Get robot status history.

**Query Parameters:**
- `limit` (number, optional) - Number of history entries to return (default: 50, max: 100)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "timestamp": "2025-10-23T16:13:47.082142",
      "status": "cleaning",
      "battery": 85,
      "error": null
    },
    ...
  ]
}
```

---

## Error Responses

All endpoints may return error responses in the following format:

```json
{
  "success": false,
  "error": "Error message describing what went wrong"
}
```

Common HTTP status codes:
- `200` - Success
- `400` - Bad Request (invalid parameters)
- `500` - Internal Server Error

---

## Notification Triggers

The system automatically sends notifications when:

1. **Error Detected**: When `error` field changes from `null` to a non-null value
2. **Low Battery**: When battery level crosses below the configured threshold

Configure notification preferences using the `/api/config` endpoint.

---

## Integration Example (Python)

```python
import requests
import json
import time

SERVER_URL = 'http://localhost:5000'

# Send status update
def update_status(status_data):
    response = requests.post(
        f'{SERVER_URL}/api/status',
        headers={'Content-Type': 'application/json'},
        data=json.dumps(status_data)
    )
    return response.json()

# Poll for commands (implement on robot side)
def get_commands():
    # In a real implementation, the server would queue commands
    # and the robot would poll for them
    pass

# Example usage
while True:
    robot_status = {
        "status": "cleaning",
        "battery": 75,
        "position": {"x": 10, "y": 20},
        "temperature": 30,
        "cleaning_mode": "auto",
        "error": None
    }
    
    result = update_status(robot_status)
    print(f"Status update: {result}")
    
    time.sleep(5)  # Update every 5 seconds
```

---

## Rate Limiting

Currently, there is no rate limiting implemented. For production use, consider implementing rate limiting to prevent abuse.

---

## Security Considerations

1. Use HTTPS in production
2. Implement authentication (API keys, JWT, etc.)
3. Validate all input data
4. Use environment variables for sensitive configuration
5. Never commit `.env` file to version control

---

## Support

For issues or questions, please refer to the [README.md](README.md) or open an issue on GitHub.
