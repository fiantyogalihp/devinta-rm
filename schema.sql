-- Database schema for Hospital Patient Records Management System

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE IF NOT EXISTS patients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    no_rm TEXT NOT NULL UNIQUE,
    nama TEXT NOT NULL,
    alamat TEXT NOT NULL,
    no_telepon TEXT NOT NULL,
    tanggal_lahir TEXT,
    nomor_identitas TEXT,
    usia INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
-- Password hash for 'admin123' using bcrypt
INSERT OR IGNORE INTO users (username, password_hash) 
VALUES ('admin', '$2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy');

-- Create indexes for better search performance
CREATE INDEX IF NOT EXISTS idx_patients_nama ON patients(nama);
CREATE INDEX IF NOT EXISTS idx_patients_no_rm ON patients(no_rm);
CREATE INDEX IF NOT EXISTS idx_patients_no_telepon ON patients(no_telepon);
CREATE INDEX IF NOT EXISTS idx_patients_nomor_identitas ON patients(nomor_identitas);
