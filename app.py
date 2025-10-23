"""
SUNSWEEP Robot REST API Server
This server provides REST API endpoints for robot status monitoring and notifications
"""

from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from datetime import datetime
import os
from dotenv import load_dotenv
from notification_service import NotificationService

# Load environment variables
load_dotenv()

app = Flask(__name__, static_folder='static', static_url_path='')
CORS(app)

# Initialize notification service
notification_service = NotificationService()

# In-memory storage for robot status (in production, use a database)
robot_status = {
    'status': 'idle',
    'battery': 100,
    'position': {'x': 0, 'y': 0},
    'temperature': 25,
    'cleaning_mode': 'auto',
    'error': None,
    'last_update': datetime.now().isoformat(),
    'history': []
}

# Configuration for notifications
notification_config = {
    'email_enabled': False,
    'sms_enabled': False,
    'email_on_error': True,
    'email_on_low_battery': True,
    'sms_on_error': True,
    'sms_on_low_battery': False,
    'low_battery_threshold': 20
}


@app.route('/')
def index():
    """Serve the main dashboard page"""
    return send_from_directory('static', 'index.html')


@app.route('/api/status', methods=['GET'])
def get_status():
    """Get current robot status"""
    return jsonify({
        'success': True,
        'data': robot_status
    })


@app.route('/api/status', methods=['POST'])
def update_status():
    """Update robot status from the robot"""
    global robot_status
    
    data = request.json
    if not data:
        return jsonify({'success': False, 'error': 'No data provided'}), 400
    
    # Store previous status for comparison
    prev_status = robot_status.copy()
    
    # Update status fields
    if 'status' in data:
        robot_status['status'] = data['status']
    if 'battery' in data:
        robot_status['battery'] = data['battery']
    if 'position' in data:
        robot_status['position'] = data['position']
    if 'temperature' in data:
        robot_status['temperature'] = data['temperature']
    if 'cleaning_mode' in data:
        robot_status['cleaning_mode'] = data['cleaning_mode']
    if 'error' in data:
        robot_status['error'] = data['error']
    
    robot_status['last_update'] = datetime.now().isoformat()
    
    # Add to history
    history_entry = {
        'timestamp': robot_status['last_update'],
        'status': robot_status['status'],
        'battery': robot_status['battery'],
        'error': robot_status['error']
    }
    robot_status['history'].append(history_entry)
    
    # Keep only last 100 history entries
    if len(robot_status['history']) > 100:
        robot_status['history'] = robot_status['history'][-100:]
    
    # Check for notification triggers
    check_and_send_notifications(prev_status, robot_status)
    
    return jsonify({
        'success': True,
        'data': robot_status
    })


@app.route('/api/command', methods=['POST'])
def send_command():
    """Send command to robot (this would be polled by the robot)"""
    data = request.json
    if not data or 'command' not in data:
        return jsonify({'success': False, 'error': 'No command provided'}), 400
    
    command = data['command']
    
    # Validate command
    valid_commands = ['start', 'stop', 'pause', 'resume', 'return_home', 'auto', 'spot', 'edge']
    if command not in valid_commands:
        return jsonify({'success': False, 'error': 'Invalid command'}), 400
    
    # In a real implementation, this would queue the command for the robot to fetch
    return jsonify({
        'success': True,
        'message': f'Command {command} queued successfully'
    })


@app.route('/api/config', methods=['GET'])
def get_config():
    """Get notification configuration"""
    return jsonify({
        'success': True,
        'data': notification_config
    })


@app.route('/api/config', methods=['POST'])
def update_config():
    """Update notification configuration"""
    global notification_config
    
    data = request.json
    if not data:
        return jsonify({'success': False, 'error': 'No data provided'}), 400
    
    # Update configuration
    for key in ['email_enabled', 'sms_enabled', 'email_on_error', 'email_on_low_battery', 
                'sms_on_error', 'sms_on_low_battery', 'low_battery_threshold']:
        if key in data:
            notification_config[key] = data[key]
    
    return jsonify({
        'success': True,
        'data': notification_config
    })


@app.route('/api/test-notification', methods=['POST'])
def test_notification():
    """Test notification system"""
    data = request.json
    notification_type = data.get('type', 'email')
    
    if notification_type == 'email':
        success = notification_service.send_email(
            'SUNSWEEP Test Notification',
            'This is a test email from your SUNSWEEP robot dashboard.'
        )
        return jsonify({
            'success': success,
            'message': 'Test email sent successfully' if success else 'Failed to send test email'
        })
    elif notification_type == 'sms':
        success = notification_service.send_sms(
            'SUNSWEEP Test: This is a test SMS from your robot dashboard.'
        )
        return jsonify({
            'success': success,
            'message': 'Test SMS sent successfully' if success else 'Failed to send test SMS'
        })
    else:
        return jsonify({'success': False, 'error': 'Invalid notification type'}), 400


@app.route('/api/history', methods=['GET'])
def get_history():
    """Get robot status history"""
    limit = request.args.get('limit', 50, type=int)
    history = robot_status['history'][-limit:]
    
    return jsonify({
        'success': True,
        'data': history
    })


def check_and_send_notifications(prev_status, current_status):
    """Check status changes and send notifications if needed"""
    
    # Check for errors
    if current_status['error'] and current_status['error'] != prev_status['error']:
        if notification_config['email_enabled'] and notification_config['email_on_error']:
            notification_service.send_email(
                'SUNSWEEP Error Alert',
                f"Your SUNSWEEP robot has encountered an error:\n\n{current_status['error']}\n\nTime: {current_status['last_update']}"
            )
        
        if notification_config['sms_enabled'] and notification_config['sms_on_error']:
            notification_service.send_sms(
                f"SUNSWEEP Alert: Error detected - {current_status['error']}"
            )
    
    # Check for low battery
    if current_status['battery'] <= notification_config['low_battery_threshold']:
        # Only send if battery just crossed the threshold
        if prev_status['battery'] > notification_config['low_battery_threshold']:
            if notification_config['email_enabled'] and notification_config['email_on_low_battery']:
                notification_service.send_email(
                    'SUNSWEEP Low Battery Alert',
                    f"Your SUNSWEEP robot battery is low: {current_status['battery']}%\n\nTime: {current_status['last_update']}"
                )
            
            if notification_config['sms_enabled'] and notification_config['sms_on_low_battery']:
                notification_service.send_sms(
                    f"SUNSWEEP Alert: Low battery {current_status['battery']}%"
                )


if __name__ == '__main__':
    host = os.getenv('FLASK_HOST', '0.0.0.0')
    port = int(os.getenv('FLASK_PORT', 5000))
    debug = os.getenv('FLASK_DEBUG', 'True') == 'True'
    
    print(f"Starting SUNSWEEP Robot Dashboard on http://{host}:{port}")
    app.run(host=host, port=port, debug=debug)
