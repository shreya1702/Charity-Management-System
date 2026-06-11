<?php
session_start();
require_once '../includes/config.php';
requireDonor();

$pageTitle = "My Donation History";
$role = 'donor';
$donorId = $_SESSION['donor_id'];

$donations = $conn->query("
    SELECT d.*, b.name as beneficiary_name, c.name as cat_name
    FROM donations d
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN categories c ON d.category_id = c.id
    WHERE d.donor_id=$donorId
    ORDER BY d.donated_at DESC
");

$total = $conn->query("SELECT COALESCE(SUM(amount),0) as t FROM donations WHERE donor_id=$donorId AND status='completed'")->fetch_assoc()['t'];

include '../includes/header.php';
?>

<div class="flex-between page-title">
    <span>📋 My Donation History</span>
    <a href="/charity-management-system/donor/donate.php" class="btn btn-success">+ New Donation</a>
</div>

<div class="alert alert-info">
    Your total contributions: <strong><?= formatCurrency($total) ?></strong> — thank you for making a difference! 🙏
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Beneficiary</th><th>Category</th><th>Amount</th><th>Method</th><th>Note</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $donations->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['beneficiary_name'] ?? 'General Fund') ?></td>
                    <td><?= htmlspecialchars($row['cat_name'] ?? '—') ?></td>
                    <td><?= formatCurrency($row['amount']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $row['payment_method'])) ?></td>
                    <td><?= htmlspecialchars($row['note'] ?? '—') ?></td>
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
