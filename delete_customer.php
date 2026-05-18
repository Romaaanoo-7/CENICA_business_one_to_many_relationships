<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
    
    // Run SELECT query to get the first and last name
    $customer = getCustomerById($pdo, $customer_id);
    if ($customer) {
        $first_name = $customer['first_name'];
        $last_name = $customer['last_name'];
        
        // Execute the DELETE query
        $sql = "DELETE FROM customers WHERE customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$customer_id]);
        
        if ($result) {
            $details = "Deleted customer: {$first_name} {$last_name}";
            insertAnActivityLog($pdo, $_SESSION['username'], 'DELETE', 'Customers', $details);
        }
    }
}
header("Location: index.php");
exit;
