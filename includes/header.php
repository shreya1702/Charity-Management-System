<?php
// includes/header.php
// Usage: include with $pageTitle and $role ('admin' or 'donor') set
$role = $role ?? 'admin';
$base = ($role === 'admin') ? '/admin' : '/donor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Charity Management System') ?></title>
    <link rel="stylesheet" href="/charity-management-system/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-brand">🤝 CharityMS</div>
    <ul class="nav-links">
        <?php if ($role === 'admin'): ?>
            <li><a href="/charity-management-system/admin/dashboard.php">Dashboard</a></li>
            <li><a href="/charity-management-system/admin/donors.php">Donors</a></li>
            <li><a href="/charity-management-system/admin/beneficiaries.php">Beneficiaries</a></li>
            <li><a href="/charity-management-system/admin/donations.php">Donations</a></li>
            <li><a href="/charity-management-system/admin/reports.php">Reports</a></li>
            <li><a href="/charity-management-system/admin/logout.php" class="btn-logout">Logout</a></li>
        <?php else: ?>
            <li><a href="/charity-management-system/donor/dashboard.php">Dashboard</a></li>
            <li><a href="/charity-management-system/donor/donate.php">Donate</a></li>
            <li><a href="/charity-management-system/donor/history.php">My Donations</a></li>
            <li><a href="/charity-management-system/donor/logout.php" class="btn-logout">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="container">
