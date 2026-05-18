<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['repair_id'])) {
    header("Location: index.php");
    exit;
}

$repair_id = $_GET['repair_id'];
$repair = getRepairCaseById($pdo, $repair_id);

if (!$repair) {
    die("Repair case not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm'])) {
        deleteRepairCase($pdo, $repair_id, $_SESSION['username']);
    }
    $customer_id = $repair['customer_id'];
    header("Location: view_repairs.php?customer_id=" . $customer_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Repair Case #<?= htmlspecialchars($repair['repair_id']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="header">
            <h1>Delete Repair Case</h1>
        </div>

        <div class="card" style="border-left: 4px solid var(--danger);">
            <h2 style="color: var(--danger); margin-top: 0;">Are you sure?</h2>
            <p>You are about to delete the following repair case. This action cannot be undone.</p>
            
            <div style="background: var(--background); padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0;">
                <p><strong>Case ID:</strong> #<?= htmlspecialchars($repair['repair_id']) ?></p>
                <p><strong>Gadget Type:</strong> <?= htmlspecialchars($repair['gadget_type']) ?></p>
                <p><strong>Issue:</strong> <?= htmlspecialchars($repair['described_issue']) ?></p>
            </div>
            
            <form method="POST" action="" style="display: flex; gap: 1rem;">
                <button type="submit" name="confirm" class="btn btn-danger" style="flex: 1;">Yes, Delete This Case</button>
                <a href="view_repairs.php?customer_id=<?= $repair['customer_id'] ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">No, Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
