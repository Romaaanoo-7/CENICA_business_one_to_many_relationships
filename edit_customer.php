<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['customer_id'])) {
    header("Location: index.php");
    exit;
}

$customer_id = $_GET['customer_id'];
$customer = getCustomerById($pdo, $customer_id);

if (!$customer) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $address = trim($_POST['address']);
    $phonenum = trim($_POST['phonenum']);
    $email_address = trim($_POST['email_address']);

    // Compare old data to submitted data
    $changes = [];
    if ($customer['first_name'] !== $first_name) $changes[] = "changed first_name from {$customer['first_name']} to {$first_name}";
    if ($customer['last_name'] !== $last_name) $changes[] = "changed last_name from {$customer['last_name']} to {$last_name}";
    if ($customer['date_of_birth'] !== $date_of_birth) $changes[] = "changed date_of_birth from {$customer['date_of_birth']} to {$date_of_birth}";
    if ($customer['address'] !== $address) $changes[] = "changed address from {$customer['address']} to {$address}";
    if ($customer['phonenum'] !== $phonenum) $changes[] = "changed phonenum from {$customer['phonenum']} to {$phonenum}";
    if ($customer['email_address'] !== $email_address) $changes[] = "changed email_address from {$customer['email_address']} to {$email_address}";
    
    if (!empty($changes)) {
        $details = "Updated Customer ID {$customer_id}: " . implode(', ', $changes);
        
        // Execute the UPDATE query
        $sql = "UPDATE customers SET first_name = ?, last_name = ?, date_of_birth = ?, address = ?, phonenum = ?, email_address = ? WHERE customer_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $customer_id]);
        
        if ($result) {
            insertAnActivityLog($pdo, $_SESSION['username'], 'UPDATE', 'Customers', $details);
        }
    }
    
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin: auto; margin-top: 50px;">
        <div class="card">
            <h2>Edit Customer</h2>
            <form method="POST" action="">
                <div class="form-group form-row">
                    <div style="flex: 1; margin-right: 1rem;">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($customer['first_name']) ?>" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($customer['last_name']) ?>" required>
                    </div>
                </div>
                <div class="form-group form-row">
                    <div style="flex: 1; margin-right: 1rem;">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($customer['date_of_birth']) ?>">
                    </div>
                    <div style="flex: 1;">
                        <label for="phonenum">Phone Number</label>
                        <input type="text" id="phonenum" name="phonenum" value="<?= htmlspecialchars($customer['phonenum']) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_address">Email Address</label>
                    <input type="email" id="email_address" name="email_address" value="<?= htmlspecialchars($customer['email_address']) ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($customer['address']) ?>">
                </div>
                <button type="submit">Update Customer</button>
                <a href="index.php" style="display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
