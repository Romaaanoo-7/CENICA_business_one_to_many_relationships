<?php
require_once 'dbConfig.php';

// --- Customer Models ---
// --- Updated Customer Models ---

function insertCustomer($pdo, $first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $added_by, $username)
{
    $sql = "INSERT INTO customers (first_name, last_name, date_of_birth, address, phonenum, email_address, added_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $added_by]);

    if ($result) {
        insertAnActivityLog($pdo, $username, 'CREATE', 'Customers', "Added a new customer: {$first_name} {$last_name}");
    }

    return $result;
}

function getAllCustomers($pdo, $searchQuery = null)
{
    if ($searchQuery) {
        $sql = "SELECT c.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM customers c LEFT JOIN employees e ON c.added_by = e.employee_id WHERE c.first_name LIKE ? OR c.last_name LIKE ? OR c.email_address LIKE ? ORDER BY c.last_updated DESC";
        $stmt = $pdo->prepare($sql);
        $searchParam = "%" . $searchQuery . "%";
        $stmt->execute([$searchParam, $searchParam, $searchParam]);
    } else {
        $sql = "SELECT c.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM customers c LEFT JOIN employees e ON c.added_by = e.employee_id ORDER BY c.customer_id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerById($pdo, $customer_id)
{
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetch();
}

// --- Updated Repair Case Models ---

function insertRepairCase($pdo, $customer_id, $gadget_type, $described_issue, $added_by, $username)
{
    $sql = "INSERT INTO repair_cases (customer_id, gadget_type, described_issue, added_by) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$customer_id, $gadget_type, $described_issue, $added_by]);
    
    if ($result) {
        insertAnActivityLog($pdo, $username, 'CREATE', 'Repairs', "Added new repair: {$gadget_type} for Customer ID {$customer_id}");
    }
    
    return $result;
}

function getRepairsByCustomerId($pdo, $customer_id, $searchQuery = null)
{
    if ($searchQuery) {
        $sql = "SELECT r.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM repair_cases r LEFT JOIN employees e ON r.added_by = e.employee_id WHERE r.customer_id = ? AND (r.gadget_type LIKE ? OR r.described_issue LIKE ?) ORDER BY r.date_added DESC";
        $stmt = $pdo->prepare($sql);
        $searchParam = "%" . $searchQuery . "%";
        $stmt->execute([$customer_id, $searchParam, $searchParam]);
    } else {
        $sql = "SELECT r.*, CONCAT(e.first_name, ' ', e.last_name) AS added_by_name FROM repair_cases r LEFT JOIN employees e ON r.added_by = e.employee_id WHERE r.customer_id = ? ORDER BY r.date_added DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRepairCaseById($pdo, $case_id)
{
    $sql = "SELECT * FROM repair_cases WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$case_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateRepairCase($pdo, $case_id, $gadget_type, $described_issue, $username)
{
    $sql_old = "SELECT * FROM repair_cases WHERE repair_id = ?";
    $stmt_old = $pdo->prepare($sql_old);
    $stmt_old->execute([$case_id]);
    $old_repair = $stmt_old->fetch(PDO::FETCH_ASSOC);

    $changes = [];
    if ($old_repair['gadget_type'] !== $gadget_type) {
        $changes[] = "changed gadget_type from {$old_repair['gadget_type']} to {$gadget_type}";
    }
    if ($old_repair['described_issue'] !== $described_issue) {
        $changes[] = "changed described_issue from {$old_repair['described_issue']} to {$described_issue}";
    }

    $sql = "UPDATE repair_cases SET gadget_type = ?, described_issue = ? WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$gadget_type, $described_issue, $case_id]);

    if ($result && !empty($changes)) {
        $details = "Updated Repair ID {$case_id}: " . implode(', ', $changes);
        insertAnActivityLog($pdo, $username, 'UPDATE', 'Repairs', $details);
    }
    
    return $result;
}

function deleteRepairCase($pdo, $case_id, $username)
{
    $sql_old = "SELECT * FROM repair_cases WHERE repair_id = ?";
    $stmt_old = $pdo->prepare($sql_old);
    $stmt_old->execute([$case_id]);
    $old_repair = $stmt_old->fetch(PDO::FETCH_ASSOC);

    $sql = "DELETE FROM repair_cases WHERE repair_id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$case_id]);

    if ($result && $old_repair) {
        $details = "Deleted repair: {$old_repair['gadget_type']}";
        insertAnActivityLog($pdo, $username, 'DELETE', 'Repairs', $details);
    }

    return $result;
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

function insertAnActivityLog($pdo, $username, $action_type, $entity, $details) {
    $sql = "INSERT INTO activity_logs (username, action_type, entity, details) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$username, $action_type, $entity, $details]);
}