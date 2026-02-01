-- Agricultural Supply Chain Management System
-- Database Creation Script

DROP DATABASE IF EXISTS agri_supply_chain;
CREATE DATABASE agri_supply_chain;
USE agri_supply_chain;

-- Table: 1 Farmer
CREATE TABLE Farmer (
    farmer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    nid INT UNIQUE,
    phone VARCHAR(20),
    district VARCHAR(50),
    upazila VARCHAR(50)
);

-- Table: 2 Buyer
CREATE TABLE Buyer (
    buyer_id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_name VARCHAR(100) NOT NULL,
    buyer_type VARCHAR(50),
    location VARCHAR(100),
    phone VARCHAR(20)
);

-- Table: 3 StorageCenter
CREATE TABLE StorageCenter (
    storage_id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    available_space INT NOT NULL
);

-- Table: 4 Transporter
CREATE TABLE Transporter (
    transporter_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_type VARCHAR(50),
    capacity INT,
    contact_no VARCHAR(20)
);

-- Table: 5 Crop
CREATE TABLE Crop (
    crop_id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    expected_price DECIMAL(10,2),
    harvest_date DATE,
    farmer_id INT,
    storage_id INT,
    transporter_id INT,
    FOREIGN KEY (farmer_id) REFERENCES Farmer(farmer_id) ON DELETE CASCADE,
    FOREIGN KEY (storage_id) REFERENCES StorageCenter(storage_id) ON DELETE SET NULL,
    FOREIGN KEY (transporter_id) REFERENCES Transporter(transporter_id) ON DELETE SET NULL
);

-- Table: 6 Orders
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_date DATE NOT NULL,
    order_status VARCHAR(50),
    total_price DECIMAL(10,2),
    crop_id INT,
    buyer_id INT,
    FOREIGN KEY (crop_id) REFERENCES Crop(crop_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES Buyer(buyer_id) ON DELETE CASCADE
);

-- Table: 7 PriceHistory
CREATE TABLE PriceHistory (
    price_id INT AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    district VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    record_date DATE NOT NULL
);

-- Insert Dummy Data for Testing
INSERT INTO Farmer (name, nid, phone, district, upazila) VALUES 
('Rahim Uddin', 1001, '01711111111', 'Dhaka', 'Savar'),
('Karim Mia', 1002, '01722222222', 'Bogra', 'Sariakandi');

INSERT INTO Crop (crop_name, quantity, expected_price, harvest_date, farmer_id) VALUES 
('Rice', 500, 45.00, '2023-11-20', 1),
('Potato', 1000, 20.00, '2023-12-10', 2);

INSERT INTO Buyer (buyer_name, buyer_type, location, phone) VALUES 
('AgroCorp Ltd', 'Wholesaler', 'Dhaka', '01833333333');
