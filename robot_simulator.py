"""
SUNSWEEP Robot Simulator
This script simulates a robot sending status updates to the server
"""

import requests
import json
import time
import random
from datetime import datetime

# Server URL
SERVER_URL = 'http://localhost:5000'

# Robot state
robot_state = {
    'status': 'idle',
    'battery': 100,
    'position': {'x': 0, 'y': 0},
    'temperature': 25,
    'cleaning_mode': 'auto',
    'error': None
}


def send_status_update():
    """Send status update to the server"""
    try:
        response = requests.post(
            f'{SERVER_URL}/api/status',
            headers={'Content-Type': 'application/json'},
            data=json.dumps(robot_state),
            timeout=5
        )
        
        if response.status_code == 200:
            result = response.json()
            if result['success']:
                print(f"[{datetime.now().strftime('%H:%M:%S')}] Status update sent successfully")
                return True
            else:
                print(f"[{datetime.now().strftime('%H:%M:%S')}] Server error: {result.get('error')}")
                return False
        else:
            print(f"[{datetime.now().strftime('%H:%M:%S')}] HTTP error: {response.status_code}")
            return False
    except requests.exceptions.ConnectionError:
        print(f"[{datetime.now().strftime('%H:%M:%S')}] Cannot connect to server. Is it running?")
        return False
    except Exception as e:
        print(f"[{datetime.now().strftime('%H:%M:%S')}] Error: {e}")
        return False


def simulate_robot_behavior():
    """Simulate robot behavior"""
    # Randomly change status
    statuses = ['idle', 'cleaning', 'charging', 'returning_home']
    if random.random() < 0.1:  # 10% chance to change status
        robot_state['status'] = random.choice(statuses)
        print(f"Status changed to: {robot_state['status']}")
    
    # Simulate battery drain when cleaning
    if robot_state['status'] == 'cleaning':
        robot_state['battery'] = max(0, robot_state['battery'] - random.uniform(0.5, 2.0))
    elif robot_state['status'] == 'charging':
        robot_state['battery'] = min(100, robot_state['battery'] + random.uniform(2.0, 5.0))
    
    # Simulate movement when cleaning
    if robot_state['status'] == 'cleaning':
        robot_state['position']['x'] += random.uniform(-2, 2)
        robot_state['position']['y'] += random.uniform(-2, 2)
        robot_state['position']['x'] = round(robot_state['position']['x'], 2)
        robot_state['position']['y'] = round(robot_state['position']['y'], 2)
    
    # Simulate temperature variation
    robot_state['temperature'] = round(25 + random.uniform(-2, 5), 1)
    
    # Simulate random errors (5% chance)
    if random.random() < 0.05 and robot_state['status'] == 'cleaning':
        errors = [
            'Obstacle detected',
            'Wheel stuck',
            'Dust bin full',
            'Sensor malfunction'
        ]
        robot_state['error'] = random.choice(errors)
        robot_state['status'] = 'error'
        print(f"ERROR: {robot_state['error']}")
    elif robot_state['status'] != 'error':
        robot_state['error'] = None
    
    # Round battery
    robot_state['battery'] = round(robot_state['battery'], 1)


def main():
    """Main simulation loop"""
    print("=" * 60)
    print("SUNSWEEP Robot Simulator")
    print("=" * 60)
    print(f"Server: {SERVER_URL}")
    print("Starting simulation...")
    print("Press Ctrl+C to stop")
    print("=" * 60)
    
    update_interval = 5  # seconds
    
    try:
        while True:
            # Simulate robot behavior
            simulate_robot_behavior()
            
            # Send status update
            send_status_update()
            
            # Display current status
            print(f"Battery: {robot_state['battery']:.1f}% | "
                  f"Position: ({robot_state['position']['x']:.1f}, {robot_state['position']['y']:.1f}) | "
                  f"Temp: {robot_state['temperature']}°C | "
                  f"Status: {robot_state['status']}")
            
            # Wait before next update
            time.sleep(update_interval)
            
    except KeyboardInterrupt:
        print("\n\nSimulation stopped by user")
        print("Final status:")
        print(json.dumps(robot_state, indent=2))


if __name__ == '__main__':
    main()
