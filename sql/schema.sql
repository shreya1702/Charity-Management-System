-- Charity Management System - Database Schema
-- Run this file to set up the database

CREATE DATABASE IF NOT EXISTS charity_db;
USE charity_db;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donors
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Beneficiaries
CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('individual','family','organization') DEFAULT 'individual',
    description TEXT,
    contact VARCHAR(100),
    address TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donation categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Donations / Transactions
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    category_id INT,
    beneficiary_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','bank_transfer','upi','cheque') DEFAULT 'cash',
    status ENUM('pending','completed','cancelled') DEFAULT 'completed',
    note TEXT,
    donated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE SET NULL
);

-- Seed data
INSERT INTO admins (name, email, password) VALUES
('Admin', 'admin@charity.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Default admin password: password

INSERT INTO categories (name, description) VALUES
('Education', 'Scholarships and educational support'),
('Food & Nutrition', 'Meals and food supplies'),
('Healthcare', 'Medical aid and treatments'),
('Shelter', 'Housing and rehabilitation'),
('Disaster Relief', 'Emergency relief funds');

INSERT INTO beneficiaries (name, category, description, contact, address, status) VALUES
('Ravi Kumar', 'individual', 'Student needing scholarship support', '9876543210', 'Dharwad, Karnataka', 'active'),
('Sundar Family', 'family', 'Family displaced due to flood', '9845012345', 'Belagavi, Karnataka', 'active'),
('Hope Foundation', 'organization', 'NGO providing meals to street children', 'hope@ngo.org', 'Bengaluru, Karnataka', 'active');

INSERT INTO donors (name, email, password, phone, address) VALUES
('Shreya AG', 'shreya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '7892654078', 'Bengaluru, Karnataka');
-- Default donor password: password

INSERT INTO donations (donor_id, category_id, beneficiary_id, amount, payment_method, status, note) VALUES
(1, 1, 1, 5000.00, 'upi', 'completed', 'Scholarship support'),
(1, 2, 3, 2500.00, 'bank_transfer', 'completed', 'Food drive contribution'),
(1, 3, 2, 1500.00, 'cash', 'completed', 'Medical aid');
