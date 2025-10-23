CREATE DATABASE IF NOT EXISTS sunsweep CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sunsweep;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','maintenance') NOT NULL DEFAULT 'maintenance',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS battery_readings (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  level_percent TINYINT UNSIGNED NOT NULL,
  voltage DECIMAL(5,2) NULL,
  current DECIMAL(6,2) NULL,
  temperature DECIMAL(5,2) NULL,
  recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS robot_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  event_type VARCHAR(50) NOT NULL,
  message VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  user_id INT NULL,
  INDEX(event_type),
  CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS cleaning_sessions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  duration_minutes INT NOT NULL,
  area_m2 DECIMAL(7,2) NULL,
  recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sensor_readings (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  sensor VARCHAR(50) NOT NULL,
  value DECIMAL(10,3) NOT NULL,
  recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(sensor)
);

CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  body TEXT NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  skey VARCHAR(100) UNIQUE NOT NULL,
  svalue TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_trail (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(255) NOT NULL,
  ip VARCHAR(64) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

DELETE FROM users WHERE username IN ('admin','maint');
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$QHfTo7y3tpEQ6ULYcE1fWeFqzIXyNtfzDOU93/NpC1Fh0L8G3Gy3C', 'admin'),
('maint', '$2y$10$LoGQ6b9B0aUKYADrYtK5Xe6Iv4Gk04vOvVHR7hXK2TfS8xsn3R0u2', 'maintenance');

INSERT INTO battery_readings (level_percent, voltage) VALUES (82,12.3),(77,12.1),(70,11.9);
INSERT INTO cleaning_sessions (duration_minutes, area_m2) VALUES (45,120.5),(30,80.0),(60,160.0);
INSERT INTO sensor_readings (sensor, value) VALUES ('ultrasonic_front',120.0),('ultrasonic_left',85.5),('ultrasonic_right',92.2);
INSERT INTO robot_logs (event_type, message) VALUES ('START','Robot started'),('DOCK','Docked for charge'),('ERROR','Brush motor jam');
INSERT INTO settings (skey, svalue) VALUES ('robot_name','SUNSWEEP-01'), ('low_battery_threshold','25')
ON DUPLICATE KEY UPDATE svalue=VALUES(svalue);
