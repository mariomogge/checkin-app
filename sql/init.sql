DROP DATABASE IF EXISTS checkin_db;
CREATE DATABASE checkin_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE checkin_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  checked_in BOOLEAN DEFAULT 0
);

CREATE TABLE desks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  location VARCHAR(100) NOT NULL,
  is_available BOOLEAN NOT NULL DEFAULT 1
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  desk_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME DEFAULT NULL,
  checkout_time DATETIME DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (desk_id) REFERENCES desks(id) ON DELETE CASCADE
);

-- --------------------------------------------
-- Beispieldaten: users
-- Passwort: secret123
-- Passwort-Hash erzeugt mit: password_hash('secret123', PASSWORD_DEFAULT)
-- --------------------------------------------

INSERT INTO users (name, password, role, checked_in) VALUES
('admin', '$2y$10$mTS3yZFJWr8YKrPLMJck7O5rZt8GgXt4x8hTfLVdLbtWmdMd3McOK', 'admin', 0),
('mario', '$2y$10$mTS3yZFJWr8YKrPLMJck7O5rZt8GgXt4x8hTfLVdLbtWmdMd3McOK', 'user', 0);

INSERT INTO desks (location, is_available) VALUES
('1. Etage - Fenster', 1),
('2. Etage - Konferenzraum', 1),
('EG - Lounge', 1);

INSERT INTO bookings (user_id, desk_id, start_time, end_time)
VALUES (
  2,
  1,
  '2025-10-18 09:00:00',
  '2025-10-18 13:00:00'
);

UPDATE desks SET is_available = 0 WHERE id = 1;
