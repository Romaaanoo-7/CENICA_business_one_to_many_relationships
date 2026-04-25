CREATE DATABASE IF NOT EXISTS it_repair_services;
USE it_repair_services;

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    address TEXT,
    phonenum VARCHAR(20)
    cx_email_address VARCHAR(150)
);

CREATE TABLE repair_cases (
    case_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    gadget_type VARCHAR(100) NOT NULL,
    described_issue TEXT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE --if a customer is deleted, so is their repair cases
);
