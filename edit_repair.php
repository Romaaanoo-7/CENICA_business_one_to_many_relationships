<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_GET['case_id'])) {
    header("Location: index.php");
    exit;
}

$case_id = $_GET['case_id'];
$repair = getRepairCaseById($pdo, $case_id);

if (!$repair) {
    die("Repair case not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gadget_type = trim($_POST['gadget_type']);
    $described_issue = trim($_POST['described_issue']);
    
    if (!empty($gadget_type) && !empty($described_issue)) {
        updateRepairCase($pdo, $case_id, $gadget_type, $described_issue);
        header("Location: view_repairs.php?customer_id=" . $repair['customer_id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Repair Case #<?= htmlspecialchars($repair['case_id']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <div class="header">
            <h1>Edit Repair Case #<?= htmlspecialchars($repair['case_id']) ?></h1>
            <a href="view_repairs.php?customer_id=<?= $repair['customer_id'] ?>" class="btn btn-secondary">Cancel</a>
        </div>

        <div class="card">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="gadget_type">Gadget Type</label>
                    <input type="text" id="gadget_type" name="gadget_type" value="<?= htmlspecialchars($repair['gadget_type']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="described_issue">Described Issue</label>
                    <textarea id="described_issue" name="described_issue" rows="6" required><?= htmlspecialchars($repair['described_issue']) ?></textarea>
                </div>
                <button type="submit" style="width: 100%;">Update Repair Case</button>
            </form>
        </div>
    </div>
</body>
</html>
