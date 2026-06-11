<?php
session_start();
if (isset($_SESSION['admin_id'])) { header("Location: /charity-management-system/admin/dashboard.php"); exit(); }
if (isset($_SESSION['donor_id'])) { header("Location: /charity-management-system/donor/dashboard.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charity Management System</title>
    <link rel="stylesheet" href="/charity-management-system/assets/css/style.css">
</head>
<body>
<div class="hero">
    <div class="hero-inner">
        <div style="font-size:3.5rem;margin-bottom:12px;">🤝</div>
        <h1>Charity Management System</h1>
        <p>Connecting donors and beneficiaries — track donations, manage operations, and make every rupee count.</p>
        <div class="hero-buttons">
            <a href="admin/login.php" class="btn-white">Admin Login</a>
            <a href="donor/login.php" class="btn-outline">Donor Login</a>
        </div>
        <p style="margin-top:22px;font-size:0.82rem;opacity:0.7;">
            New donor? <a href="donor/register.php" style="color:#fff;font-weight:700;text-decoration:underline;">Register here</a>
        </p>
    </div>
</div>
</body>
</html>
