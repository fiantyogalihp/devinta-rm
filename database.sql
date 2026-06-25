-- Database schema for Hospital Patient Records Management System
-- For MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS hospital_rm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_rm;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Patients table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    tanggal_lahir DATE NULL,
    nomor_identitas VARCHAR(50) NULL,
    usia INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama (nama),
    INDEX idx_no_rm (no_rm),
    INDEX idx_no_telepon (no_telepon),
    INDEX idx_nomor_identitas (nomor_identitas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (username: admin, password: admin123)
-- Password hash for 'admin123' using PHP password_hash()
INSERT INTO users (username, password_hash) 
VALUES ('admin', '$2y$10$1CLHmWzlhdvwS0ViychHSe2kaZW8jaqQZ9rC.POXNxikpeBtlJ7mW')
ON DUPLICATE KEY UPDATE username=username;
