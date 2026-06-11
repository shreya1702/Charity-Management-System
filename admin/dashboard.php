<?php
session_start();
require_once '../includes/config.php';
requireAdmin();

$pageTitle = "Dashboard – Admin";
$role = 'admin';

// Stats
$totalDonors       = $conn->query("SELECT COUNT(*) as c FROM donors")->fetch_assoc()['c'];
$totalBeneficiaries= $conn->query("SELECT COUNT(*) as c FROM beneficiaries WHERE status='active'")->fetch_assoc()['c'];
$totalDonations    = $conn->query("SELECT COUNT(*) as c FROM donations WHERE status='completed'")->fetch_assoc()['c'];
$totalAmount       = $conn->query("SELECT COALESCE(SUM(amount),0) as s FROM donations WHERE status='completed'")->fetch_assoc()['s'];

// Recent donations
$recent = $conn->query("
    SELECT d.id, do.name as donor_name, b.name as beneficiary_name,
           c.name as category, d.amount, d.payment_method, d.status, d.donated_at
    FROM donations d
    JOIN donors do ON d.donor_id = do.id
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN categories c ON d.category_id = c.id
    ORDER BY d.donated_at DESC LIMIT 8
");

include '../includes/header.php';
?>

<div class="page-title">📊 Dashboard</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-number"><?= $totalDonors ?></div>
        <div class="stat-label">Total Donors</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">🏠</div>
        <div class="stat-number"><?= $totalBeneficiaries ?></div>
        <div class="stat-label">Active Beneficiaries</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon">💸</div>
        <div class="stat-number"><?= $totalDonations ?></div>
        <div class="stat-label">Total Donations</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">💰</div>
        <div class="stat-number"><?= formatCurrency($totalAmount) ?></div>
        <div class="stat-label">Total Amount Raised</div>
    </div>
</div>

<div class="card">
    <div class="card-title">Recent Donations</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Donor</th>
                    <th>Beneficiary</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['donor_name']) ?></td>
                    <td><?= htmlspecialchars($row['beneficiary_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['category'] ?? '—') ?></td>
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
</div>

<?php include '../includes/footer.php'; ?>
