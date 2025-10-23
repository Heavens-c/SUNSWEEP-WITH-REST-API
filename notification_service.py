"""
Notification Service for SUNSWEEP Robot
Handles email and SMS notifications
"""

import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import os
from dotenv import load_dotenv

load_dotenv()


class NotificationService:
    """Service to handle email and SMS notifications"""
    
    def __init__(self):
        # Email configuration
        self.smtp_server = os.getenv('SMTP_SERVER', 'smtp.gmail.com')
        self.smtp_port = int(os.getenv('SMTP_PORT', 587))
        self.smtp_username = os.getenv('SMTP_USERNAME', '')
        self.smtp_password = os.getenv('SMTP_PASSWORD', '')
        self.notification_email = os.getenv('NOTIFICATION_EMAIL', '')
        
        # SMS configuration (Twilio)
        self.twilio_account_sid = os.getenv('TWILIO_ACCOUNT_SID', '')
        self.twilio_auth_token = os.getenv('TWILIO_AUTH_TOKEN', '')
        self.twilio_phone = os.getenv('TWILIO_PHONE_NUMBER', '')
        self.notification_phone = os.getenv('NOTIFICATION_PHONE', '')
        
        # Initialize Twilio client if credentials are available
        self.twilio_client = None
        if self.twilio_account_sid and self.twilio_auth_token:
            try:
                from twilio.rest import Client
                self.twilio_client = Client(self.twilio_account_sid, self.twilio_auth_token)
            except ImportError:
                print("Warning: Twilio library not installed. SMS notifications will not work.")
            except Exception as e:
                print(f"Warning: Failed to initialize Twilio client: {e}")
    
    def send_email(self, subject, body):
        """Send email notification"""
        if not self.smtp_username or not self.smtp_password or not self.notification_email:
            print("Email configuration is incomplete. Skipping email notification.")
            return False
        
        try:
            # Create message
            msg = MIMEMultipart()
            msg['From'] = self.smtp_username
            msg['To'] = self.notification_email
            msg['Subject'] = subject
            
            msg.attach(MIMEText(body, 'plain'))
            
            # Send email
            server = smtplib.SMTP(self.smtp_server, self.smtp_port)
            server.starttls()
            server.login(self.smtp_username, self.smtp_password)
            text = msg.as_string()
            server.sendmail(self.smtp_username, self.notification_email, text)
            server.quit()
            
            print(f"Email sent successfully to {self.notification_email}")
            return True
        except Exception as e:
            print(f"Failed to send email: {e}")
            return False
    
    def send_sms(self, message):
        """Send SMS notification using Twilio"""
        if not self.twilio_client:
            print("Twilio client not initialized. Skipping SMS notification.")
            return False
        
        if not self.twilio_phone or not self.notification_phone:
            print("SMS configuration is incomplete. Skipping SMS notification.")
            return False
        
        try:
            message = self.twilio_client.messages.create(
                body=message,
                from_=self.twilio_phone,
                to=self.notification_phone
            )
            print(f"SMS sent successfully to {self.notification_phone}")
            return True
        except Exception as e:
            print(f"Failed to send SMS: {e}")
            return False
