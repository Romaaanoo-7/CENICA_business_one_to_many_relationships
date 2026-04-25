<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['customer_id'])) {
    header("Location: index.php");
    exit;
}

$customer_id = $_GET['customer_id'];
$customer = getCustomerById($pdo, $customer_id);

if (!$customer) {
    die("Customer not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gadget_type = trim($_POST['gadget_type']);
    $described_issue = trim($_POST['described_issue']);
    
    if (!empty($gadget_type) && !empty($described_issue)) {
        insertRepairCase($pdo, $customer_id, $gadget_type, $described_issue);
        header("Location: view_repairs.php?customer_id=" . $customer_id);
        exit;
    }
}

$repairs = getRepairsByCustomerId($pdo, $customer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repairs for <?= htmlspecialchars($customer['first_name']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Repairs for <span style="color: var(--primary);"><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></span></h1>
            <a href="index.php" class="btn btn-secondary">Back to Customers</a>
        </div>

        <div class="card">
            <h2>Log New Repair Case</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="gadget_type">Gadget Type</label>
                    <input type="text" id="gadget_type" name="gadget_type" required placeholder="e.g. iPhone 13, Dell XPS 15">
                </div>
                <div class="form-group">
                    <label for="described_issue">Described Issue</label>
                    <textarea id="described_issue" name="described_issue" rows="4" required placeholder="Describe the problem in detail..."></textarea>
                </div>
                <button type="submit">Log Repair Case</button>
            </form>
        </div>

        <h2>Repair History</h2>
        <?php if (empty($repairs)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--text-muted); font-size: 1.1rem;">No repair cases logged for this customer yet.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Gadget Type</th>
                            <th>Issue</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairs as $repair): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($repair['case_id']) ?></td>
                            <td><strong><?= htmlspecialchars($repair['gadget_type']) ?></strong></td>
                            <td><?= htmlspecialchars($repair['described_issue']) ?></td>
                            <td><?= date('M d, Y', strtotime($repair['date_added'])) ?></td>
                            <td class="action-links">
                                <a href="edit_repair.php?case_id=<?= $repair['case_id'] ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="delete_repair.php?case_id=<?= $repair['case_id'] ?>" class="btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
