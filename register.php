<!--handles creating new employee accounts-->
<?php
session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    if (!empty($username) && !empty($password) && !empty($first_name) && !empty($last_name)) {
        if (registerEmployee($pdo, $username, $password, $first_name, $last_name)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Registration failed. Username might already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Employee</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container" style="max-width: 400px; margin-top: 50px;">
        <h2>Employee Registration</h2>
        <?php if (isset($error))
            echo "<p style='color: red;'>$error</p>"; ?>
        <div class="card">
            <form method="POST" action="">
                <div class="form-group"><label>First Name</label><input type="text" name="first_name" required></div>
                <div class="form-group"><label>Last Name</label><input type="text" name="last_name" required></div>
                <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <button type="submit" style="width: 100%;">Register</button>
            </form>
            <p style="text-align: center; margin-top: 15px;"><a href="login.php">Already have an account? Login
                    here.</a></p>
        </div>
    </div>
</body>

</html>