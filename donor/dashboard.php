<?php
session_start();
require_once '../includes/config.php';
requireDonor();

$pageTitle = "My Dashboard";
$role = 'donor';
$donorId = $_SESSION['donor_id'];

$stats = $conn->query("
    SELECT COUNT(*) as total_donations,
           COALESCE(SUM(amount),0) as total_amount,
           COALESCE(MAX(amount),0) as largest
    FROM donations WHERE donor_id=$donorId AND status='completed'
")->fetch_assoc();

$recent = $conn->query("
    SELECT d.*, b.name as beneficiary_name, c.name as cat_name
    FROM donations d
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN categories c ON d.category_id = c.id
    WHERE d.donor_id=$donorId
    ORDER BY d.donated_at DESC LIMIT 5
");

include '../includes/header.php';
?>

<div class="page-title">👋 Welcome, <?= htmlspecialchars($_SESSION['donor_name']) ?>!</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">💸</div>
        <div class="stat-number"><?= $stats['total_donations'] ?></div>
        <div class="stat-label">Total Donations Made</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-number"><?= formatCurrency($stats['total_amount']) ?></div>
        <div class="stat-label">Total Amount Donated</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon">🏆</div>
        <div class="stat-number"><?= formatCurrency($stats['largest']) ?></div>
        <div class="stat-label">Largest Single Donation</div>
    </div>
</div>

<div class="flex-between" style="margin-bottom:16px;">
    <div class="card-title" style="margin:0;">Recent Donations</div>
    <a href="/charity-management-system/donor/donate.php" class="btn btn-success">+ Make a Donation</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Beneficiary</th><th>Category</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['beneficiary_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['cat_name'] ?? '—') ?></td>
                    <td><?= formatCurrency($row['amount']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $row['payment_method'])) ?></td>
                    <td>
                        <span class="badge badge-<?= $row['status'] === 'completed' ? 'success' : ($row['status'] === 'pending' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($row['donated_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:14px;">
        <a href="/charity-management-system/donor/history.php" class="btn btn-primary btn-sm">View All Donations →</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
