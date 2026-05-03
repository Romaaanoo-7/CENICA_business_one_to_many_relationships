<?php
require_once 'dbConfig.php';

// --- Customer Models ---
// --- Updated Customer Models ---

function insertCustomer($pdo, $first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $added_by)
{
    $sql = "INSERT INTO customers (first_name, last_name, date_of_birth, address, phonenum, email_address, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $added_by]);
}

function getAllCustomers($pdo)
{
    $sql = "SELECT c.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM customers c LEFT JOIN employees e ON c.added_by = e.employee_id ORDER BY c.customer_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerById($pdo, $customer_id) {
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetch();
}

// --- Updated Repair Case Models ---

function insertRepairCase($pdo, $customer_id, $gadget_type, $described_issue, $added_by)
{
    $sql = "INSERT INTO repair_cases (customer_id, gadget_type, described_issue, added_by) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$customer_id, $gadget_type, $described_issue, $added_by]);
}

function getRepairsByCustomerId($pdo, $customer_id)
{
    $sql = "SELECT r.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM repair_cases r LEFT JOIN employees e ON r.added_by = e.employee_id WHERE r.customer_id = ? ORDER BY r.date_added DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRepairCaseById($pdo, $case_id)
{
    $sql = "SELECT * FROM repair_cases WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$case_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateRepairCase($pdo, $case_id, $gadget_type, $described_issue)
{
    $sql = "UPDATE repair_cases SET gadget_type = ?, described_issue = ? WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$gadget_type, $described_issue, $case_id]);
}

function deleteRepairCase($pdo, $case_id)
{
    $sql = "DELETE FROM repair_cases WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$case_id]);
}

// added Employee Authentication Models

function registerEmployee($pdo, $username, $password, $first_name, $last_name)
{
    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO employees (username, password, first_name, last_name) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $hashedPassword, $first_name, $last_name]);
}

function loginEmployee($pdo, $username, $password)
{
    $sql = "SELECT * FROM employees WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $employee = $stmt->fetch();

    // Verify the hashed password against the user input
    if ($employee && password_verify($password, $employee['password'])) {
        return $employee; // Return employee data if successful
    }
    return false; // Return false if login fails
}