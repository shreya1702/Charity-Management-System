<?php
session_start();
require_once '../includes/config.php';

if (isDonorLoggedIn()) { header("Location: dashboard.php"); exit(); }

$error = $msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($conn, $_POST['name']);
    $email   = sanitize($conn, $_POST['email']);
    $phone   = sanitize($conn, $_POST['phone']);
    $address = sanitize($conn, $_POST['address']);
    $pass    = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!$name || !$email || !$pass) {
        $error = "Name, email and password are required.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, address) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $email, $hashed, $phone, $address);
        if ($stmt->execute()) {
            $msg = "Registration successful! You can now login.";
        } else {
            $error = "Email already registered. Please use a different email.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Donor</title>
    <link rel="stylesheet" href="/charity-management-system/assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card" style="max-width:480px;">
        <h2>🤝 Donor Registration</h2>
        <p class="sub">Create your account to start donating</p>

        <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-grid" style="grid-template-columns:1fr 1fr;">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px;padding:11px;">Register</button>
        </form>

        <div class="login-switch">
            Already have an account? <a href="/charity-management-system/donor/login.php">Login here</a>
            &nbsp;|&nbsp; <a href="/charity-management-system/index.php">← Home</a>
        </div>
    </div>
</div>
<script src="/charity-management-system/assets/js/main.js"></script>
</body>
</html>
