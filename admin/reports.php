<?php
session_start();
require_once '../includes/config.php';
requireAdmin();

$pageTitle = "Reports – Admin";
$role = 'admin';

// Overall stats
$stats = $conn->query("
    SELECT
        COUNT(*) as total_donations,
        COALESCE(SUM(amount),0) as total_amount,
        COALESCE(AVG(amount),0) as avg_amount,
        COUNT(DISTINCT donor_id) as unique_donors
    FROM donations WHERE status='completed'
")->fetch_assoc();

// By category
$byCategory = $conn->query("
    SELECT c.name, COUNT(d.id) as count, COALESCE(SUM(d.amount),0) as total
    FROM donations d
    LEFT JOIN categories c ON d.category_id = c.id
    WHERE d.status='completed'
    GROUP BY c.name
    ORDER BY total DESC
");

// By payment method
$byMethod = $conn->query("
    SELECT payment_method, COUNT(*) as count, COALESCE(SUM(amount),0) as total
    FROM donations WHERE status='completed'
    GROUP BY payment_method ORDER BY total DESC
");

// Monthly trend (last 6 months)
$monthly = $conn->query("
    SELECT DATE_FORMAT(donated_at, '%b %Y') as month,
           COUNT(*) as count,
           COALESCE(SUM(amount),0) as total
    FROM donations
    WHERE status='completed' AND donated_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(donated_at, '%Y-%m')
    ORDER BY donated_at ASC
");

// Top donors
$topDonors = $conn->query("
    SELECT dn.name, COUNT(d.id) as donations, SUM(d.amount) as total
    FROM donations d
    JOIN donors dn ON d.donor_id = dn.id
    WHERE d.status='completed'
    GROUP BY d.donor_id
    ORDER BY total DESC LIMIT 5
");

include '../includes/header.php';
?>

<div class="page-title">📈 Reports & Analytics</div>

<div class="report-grid">
    <div class="report-card">
        <h3><?= formatCurrency($stats['total_amount']) ?></h3>
        <p>Total Amount Raised</p>
    </div>
    <div class="report-card">
        <h3><?= $stats['total_donations'] ?></h3>
        <p>Completed Donations</p>
    </div>
    <div class="report-card">
        <h3><?= $stats['unique_donors'] ?></h3>
        <p>Unique Donors</p>
    </div>
    <div class="report-card">
        <h3><?= formatCurrency($stats['avg_amount']) ?></h3>
        <p>Average Donation</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    <!-- By Category -->
    <div class="card">
        <div class="card-title">Donations by Category</div>
        <table>
            <thead><tr><th>Category</th><th>Count</th><th>Total</th></tr></thead>
            <tbody>
                <?php while ($row = $byCategory->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name'] ?? 'Uncategorized') ?></td>
                    <td><?= $row['count'] ?></td>
                    <td><?= formatCurrency($row['total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- By Payment Method -->
    <div class="card">
        <div class="card-title">Donations by Payment Method</div>
        <table>
            <thead><tr><th>Method</th><th>Count</th><th>Total</th></tr></thead>
            <tbody>
                <?php while ($row = $byMethod->fetch_assoc()): ?>
                <tr>
                    <td><?= ucfirst(str_replace('_', ' ', $row['payment_method'])) ?></td>
                    <td><?= $row['count'] ?></td>
                    <td><?= formatCurrency($row['total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <!-- Monthly Trend -->
    <div class="card">
        <div class="card-title">Monthly Trend (Last 6 Months)</div>
        <table>
            <thead><tr><th>Month</th><th>Donations</th><th>Amount</th></tr></thead>
            <tbody>
                <?php while ($row = $monthly->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['month'] ?></td>
                    <td><?= $row['count'] ?></td>
                    <td><?= formatCurrency($row['total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Top Donors -->
    <div class="card">
        <div class="card-title">Top 5 Donors</div>
        <table>
            <thead><tr><th>Donor</th><th>Donations</th><th>Total</th></tr></thead>
            <tbody>
                <?php while ($row = $topDonors->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['donations'] ?></td>
                    <td><?= formatCurrency($row['total']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
