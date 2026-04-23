-- Database: db_kelulusan
CREATE DATABASE IF NOT EXISTS db_kelulusan;
USE db_kelulusan;

-- Table admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table siswa
CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    link_skl TEXT NOT NULL,
    status ENUM('LULUS', 'TIDAK LULUS') DEFAULT 'LULUS'
);

-- Table pengaturan
CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sekolah VARCHAR(100) DEFAULT 'SMA Negeri Contoh',
    alamat_sekolah TEXT,
    logo VARCHAR(255) DEFAULT 'default-logo.png',
    tgl_pengumuman DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin: admin / admin123
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$a50jVPS3rX8mjnwIiJyTWeodhMw7lhqWREnEHIcqdi6B5yILCDsvW');

INSERT INTO pengaturan (nama_sekolah, alamat_sekolah, logo, tgl_pengumuman)
VALUES ('SMA MAJU JAYA', 'Jl. Pendidikan No. 1, Jakarta', 'logo.png', '2025-06-01 10:00:00');
