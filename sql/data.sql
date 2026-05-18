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

--new tables for security--
CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE customers
ADD added_by INT,
ADD last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD FOREIGN KEY (added_by) REFERENCES employees(employee_id);

ALTER TABLE repair_cases
ADD added_by INT,
ADD last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD FOREIGN KEY (added_by) REFERENCES employees(employee_id);


CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    entity VARCHAR(50) NOT NULL,
    details TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);