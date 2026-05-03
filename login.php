<!--handles employee login,validates credentials and starts the session-->
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

    $employee = loginEmployee($pdo, $username, $password);

    if ($employee) {
        $_SESSION['employee_id'] = $employee['employee_id'];
        $_SESSION['full_name'] = $employee['first_name'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container" style="max-width: 400px; margin-top: 50px;">
        <h2>IT Repair Services - Login</h2>
        <?php if (isset($error))
            echo "<p style='color: red;'>$error</p>"; ?>
        <div class="card">
            <form method="POST" action="">
                <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <button type="submit" style="width: 100%;">Login</button>
            </form>
            <p style="text-align: center; margin-top: 15px;"><a href="register.php">Need an account? Register here.</a>
            </p>
        </div>
    </div>
</body>

</html>