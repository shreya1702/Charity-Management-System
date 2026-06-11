<?php
session_start();
require_once '../includes/config.php';

if (isAdminLoggedIn()) { header("Location: dashboard.php"); exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id']   = $row['id'];
                $_SESSION['admin_name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            }
        }
        $error = "Invalid email or password.";
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – CharityMS</title>
    <link rel="stylesheet" href="/charity-management-system/assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <h2>🔐 Admin Login</h2>
        <p class="sub">Charity Management System</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@charity.com" required autofocus
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-top:14px;">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary">Login as Admin</button>
        </form>

        <div class="login-switch" style="margin-top:16px;">
            <a href="/charity-management-system/index.php">← Back to Home</a>
        </div>
    </div>
</div>
<script src="/charity-management-system/assets/js/main.js"></script>
</body>
</html>
