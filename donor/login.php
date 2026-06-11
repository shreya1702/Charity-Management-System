<?php
session_start();
require_once '../includes/config.php';

if (isDonorLoggedIn()) { header("Location: dashboard.php"); exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $stmt = $conn->prepare("SELECT id, name, password FROM donors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($pass, $row['password'])) {
                $_SESSION['donor_id']   = $row['id'];
                $_SESSION['donor_name'] = $row['name'];
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
    <title>Donor Login – CharityMS</title>
    <link rel="stylesheet" href="/charity-management-system/assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <h2>🤝 Donor Login</h2>
        <p class="sub">Welcome back! Login to manage your donations</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required autofocus
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-top:14px;">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-success">Login</button>
        </form>

        <div class="login-switch">
            Don't have an account? <a href="/charity-management-system/donor/register.php">Register here</a>
            &nbsp;|&nbsp; <a href="/charity-management-system/index.php">← Home</a>
        </div>
    </div>
</div>
<script src="/charity-management-system/assets/js/main.js"></script>
</body>
</html>
