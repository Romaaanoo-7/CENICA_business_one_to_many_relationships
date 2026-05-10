<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $address = trim($_POST['address']);
    $phonenum = trim($_POST['phonenum']);
    $email_address = trim($_POST['email_address']);

    if (!empty($first_name) && !empty($last_name)) {
        insertCustomer($pdo, $first_name, $last_name, $date_of_birth, $address, $phonenum, $email_address, $_SESSION['employee_id']);
        header("Location: index.php");
        exit;
    }
}

$customers = getAllCustomers($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Repair Services - Customers</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container" style="max-width: 1200px; width: 95%; margin: auto;">
        <div class="header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--primary); color: white; border-radius: 8px; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 1.5rem;">IT Repair Services - Customers</h1>
            <div>
                <span style="margin-right: 15px; font-weight: bold;">
                    Welcome,
                    <?= htmlspecialchars($_SESSION['full_name'] ?? 'Employee') ?>
                </span>
                <a href="logout.php"
                    style="background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">Logout</a>
            </div>
        </div>

        <div class="card">
            <h2>Add New Customer</h2>
            <form method="POST" action="">
                <div class="form-group form-row">
                    <div style="flex: 1; margin-right: 1rem;">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="form-group form-row">
                    <div style="flex: 1; margin-right: 1rem;">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth">
                    </div>
                    <div style="flex: 1;">
                        <label for="phonenum">Phone Number</label>
                        <input type="text" id="phonenum" name="phonenum">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_address">Email Address</label>
                    <input type="email" id="email_address" name="email_address">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address">
                </div>
                <button type="submit">Add Customer</button>
            </form>
        </div>

        <h2>Customers List</h2>
        <div class="table-container">
            <div style="overflow-x: auto; width: 100%;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Added By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No customers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                                    <td><strong>
                                            <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>
                                        </strong>
                                    </td>
                                    <td><?= htmlspecialchars($customer['phonenum']) ?></td>
                                    <td><?= htmlspecialchars($customer['email_address']) ?></td>
                                    <td><?= htmlspecialchars($customer['added_by_name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($customer['last_updated']) ?></td>
                                    <td class="action-links">
                                        <a href="view_repairs.php?customer_id=<?= $customer['customer_id'] ?>"
                                            class="btn btn-primary btn-sm">View Repairs</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>