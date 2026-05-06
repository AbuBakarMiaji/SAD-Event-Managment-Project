-- Online Event Management System Database Schema
CREATE DATABASE IF NOT EXISTS event_managements CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_managements;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin','customer','manager','staff') DEFAULT 'customer',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Event Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category ENUM('wedding','birthday','conference','corporate','other') DEFAULT 'other',
    description TEXT,
    venue VARCHAR(200),
    capacity INT DEFAULT 100,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(5,2) DEFAULT 0,
    image VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Package Services Table
CREATE TABLE IF NOT EXISTS package_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    package_id INT NOT NULL,
    manager_id INT DEFAULT NULL,
    event_date DATE NOT NULL,
    guest_count INT DEFAULT 50,
    special_requests TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','approved','ongoing','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (package_id) REFERENCES packages(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('cash','card','bank_transfer','mobile') DEFAULT 'cash',
    status ENUM('pending','paid','refunded') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Tasks Table
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    staff_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    service_type ENUM('catering','decoration','venue_setup','photography','music','other') DEFAULT 'other',
    status ENUM('pending','in_progress','completed') DEFAULT 'pending',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (staff_id) REFERENCES users(id)
);

-- Feedback Table
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Default Admin User (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Super Admin', 'admin@eventpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample Packages
INSERT INTO packages (name, category, description, venue, capacity, price, discount) VALUES
('Royal Wedding Package', 'wedding', 'A luxurious wedding experience with full decoration, catering, and photography.', 'Grand Ballroom, Dhaka', 500, 250000.00, 10),
('Birthday Bash Package', 'birthday', 'Fun-filled birthday celebration with cake, decorations, and entertainment.', 'Event Hall, Gulshan', 100, 25000.00, 5),
('Corporate Summit Package', 'conference', 'Professional conference setup with AV equipment, seating, and catering.', 'Conference Center, Motijheel', 300, 80000.00, 0),
('Premium Corporate Package', 'corporate', 'All-inclusive corporate event with branding, catering, and team activities.', 'Rooftop Venue, Banani', 200, 120000.00, 8);

INSERT INTO package_services (package_id, service_name, description) VALUES
(1, 'Full Decoration', 'Floral arrangements, lighting, stage setup'),
(1, 'Catering', '5-course meal for all guests'),
(1, 'Photography & Video', 'Professional photographers and videographers'),
(1, 'Music & Entertainment', 'Live band and DJ'),
(2, 'Decoration', 'Birthday themed decoration'),
(2, 'Cake', 'Custom 3-tier cake'),
(2, 'Catering', 'Snacks and meals for guests'),
(3, 'AV Equipment', 'Projectors, microphones, sound system'),
(3, 'Catering', 'Lunch and refreshments'),
(3, 'Seating', 'Conference-style seating arrangement'),
(4, 'Branding', 'Custom banners and branding materials'),
(4, 'Full Catering', 'All meals and beverages'),
(4, 'Team Activities', 'Guided team building exercises');