<?php
require_once 'dbConfig.php';

// --- Customer Models ---
function insertCustomer($pdo, $first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address)
{
    $sql = "INSERT INTO customers (first_name, last_name, date_of_birth, address, phonenum, email_address) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address]);
}

function getAllCustomers($pdo)
{
    $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCustomerById($pdo, $customer_id)
{
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Repair Case Models 
function insertRepairCase($pdo, $customer_id, $gadget_type, $described_issue)
{
    $sql = "INSERT INTO repair_cases (customer_id, gadget_type, described_issue) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$customer_id, $gadget_type, $described_issue]);
}

function getRepairsByCustomerId($pdo, $customer_id)
{
    $sql = "SELECT * FROM repair_cases WHERE customer_id = ? ORDER BY date_added DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRepairCaseById($pdo, $case_id)
{
    $sql = "SELECT * FROM repair_cases WHERE case_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$case_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateRepairCase($pdo, $case_id, $gadget_type, $described_issue)
{
    $sql = "UPDATE repair_cases SET gadget_type = ?, described_issue = ? WHERE case_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$gadget_type, $described_issue, $case_id]);
}

function deleteRepairCase($pdo, $case_id)
{
    $sql = "DELETE FROM repair_cases WHERE case_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$case_id]);
}
?>