<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}
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
        insertRepairCase($pdo, $customer_id, $gadget_type, $described_issue, $_SESSION['employee_id'], $_SESSION['username']);
        header("Location: view_repairs.php?customer_id=" . $customer_id);
        exit;
    }
}

$searchQuery = isset($_GET['search']) ? $_GET['search'] : null;
$repairs = getRepairsByCustomerId($pdo, $customer_id, $searchQuery);
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
        <div class="header"
            style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--primary); color: white; border-radius: 8px; margin-bottom: 20px;">
            <h1 style="margin: 0; font-size: 1.5rem;">Repairs For
                <?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?>
            </h1>
            <div>
                <span style="margin-right: 15px; font-weight: bold;">
                    Active Session: <?= htmlspecialchars($_SESSION['full_name'] ?? 'Employee') ?>
                </span>
                <a href="logout.php"
                    style="background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;">Logout</a>
            </div>
        </div>

        <a href="index.php"
            style="display: inline-block; margin-bottom: 15px; text-decoration: none; color: #555; font-weight: bold;">&larr;
            Back to Customers List</a>

        <div class="card">
            <h2>Log New Repair Case</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="gadget_type">Gadget Type</label>
                    <input type="text" id="gadget_type" name="gadget_type" required
                        placeholder="e.g. iPhone 13, Dell XPS 15">
                </div>
                <div class="form-group">
                    <label for="described_issue">Described Issue</label>
                    <textarea id="described_issue" name="described_issue" rows="4" required
                        placeholder="Describe the problem in detail..."></textarea>
                </div>
                <button type="submit">Log Repair Case</button>
            </form>
        </div>

        <h2>Repair History</h2>
        <form method="GET" action="" style="margin-bottom: 15px; display: flex; gap: 10px;">
            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($_GET['customer_id']) ?>">
            <input type="text" name="search" placeholder="Search repairs..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 8px; flex: 1; max-width: 300px; border: 1px solid #ccc; border-radius: 4px;">
            <button type="submit" style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                <a href="view_repairs.php?customer_id=<?= htmlspecialchars($_GET['customer_id']) ?>" style="padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Clear</a>
            <?php endif; ?>
        </form>
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
                            <th>Added By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairs as $repair): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($repair['repair_id']) ?></td>
                                <td><strong><?= htmlspecialchars($repair['gadget_type']) ?></strong></td>
                                <td><?= htmlspecialchars($repair['described_issue']) ?></td>
                                <td><?= date('M d, Y', strtotime($repair['date_added'])) ?></td>
                                <td><?= htmlspecialchars($repair['added_by_name'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars($repair['last_updated']) ?></td>
                                <td class="action-links">
                                    <a href="edit_repair.php?repair_id=<?= $repair['repair_id'] ?>"
                                        style="margin-right: 10px; color: #0d6efd; font-weight: bold; text-decoration: none;">Update</a>
                                    <a href="delete_repair.php?repair_id=<?= $repair['repair_id'] ?>"
                                        style="color: #dc3545; font-weight: bold; text-decoration: none;">Delete</a>
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