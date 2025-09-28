-- Hands With Care Database Schema

-- -------------------------
-- Admins (Owner + Admins)
-- -------------------------
DROP TABLE IF EXISTS admins;
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('owner','admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default owner account
-- Username: owner
-- Password: owner123
INSERT INTO admins (username, password, role)
VALUES (
    'owner',
    '$2y$10$9aYXh1PdrK9ucnV0U3u4pe4XgMj/4V.3R1CyhAZuMCjZojDoW0IfK',
    'owner'
);

-- -------------------------
-- Donations
-- -------------------------
DROP TABLE IF EXISTS donations;
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100),
    donor_name VARCHAR(255),
    email VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    program VARCHAR(255),
    message TEXT,
    status VARCHAR(50) DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

-- -------------------------
-- Programs
-- -------------------------
DROP TABLE IF EXISTS programs;
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------
-- Stories
-- -------------------------
DROP TABLE IF EXISTS stories;
CREATE TABLE stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------
-- Contacts
-- -------------------------
DROP TABLE IF EXISTS contacts;
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
