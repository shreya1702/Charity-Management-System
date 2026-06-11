<?php
session_start();
require_once '../includes/config.php';
requireAdmin();

$pageTitle = "Donations – Admin";
$role = 'admin';
$msg = $error = '';

// Update status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $status = sanitize($conn, $_GET['status']);
    if (in_array($status, ['pending','completed','cancelled'])) {
        $conn->query("UPDATE donations SET status='$status' WHERE id=$id");
        $msg = "Donation status updated.";
    }
}

// Filters
$where = "1=1";
if (!empty($_GET['donor_id'])) $where .= " AND d.donor_id=" . (int)$_GET['donor_id'];
if (!empty($_GET['status_filter'])) $where .= " AND d.status='" . sanitize($conn, $_GET['status_filter']) . "'";
if (!empty($_GET['from'])) $where .= " AND DATE(d.donated_at) >= '" . sanitize($conn, $_GET['from']) . "'";
if (!empty($_GET['to']))   $where .= " AND DATE(d.donated_at) <= '" . sanitize($conn, $_GET['to']) . "'";

$donations = $conn->query("
    SELECT d.*, dn.name as donor_name, b.name as beneficiary_name, c.name as cat_name
    FROM donations d
    JOIN donors dn ON d.donor_id = dn.id
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN categories c ON d.category_id = c.id
    WHERE $where
    ORDER BY d.donated_at DESC
");

$donors = $conn->query("SELECT id, name FROM donors ORDER BY name");
include '../includes/header.php';
?>

<div class="page-title">💸 All Donations</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<!-- Filters -->
<div class="card" style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="min-width:160px;">
            <label>Donor</label>
            <select name="donor_id">
                <option value="">All Donors</option>
                <?php while ($d = $donors->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>" <?= ($_GET['donor_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group" style="min-width:140px;">
            <label>Status</label>
            <select name="status_filter">
                <option value="">All</option>
                <option value="completed" <?= ($_GET['status_filter'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="pending"   <?= ($_GET['status_filter'] ?? '') === 'pending'   ? 'selected' : '' ?>>Pending</option>
                <option value="cancelled" <?= ($_GET['status_filter'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="form-group">
            <label>From Date</label>
            <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>To Date</label>
            <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
        </div>
        <div class="form-group" style="justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="donations.php" class="btn" style="background:#eee;color:#333;margin-left:8px;">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Donor</th><th>Beneficiary</th><th>Category</th><th>Amount</th><th>Method</th><th>Note</th><th>Status</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                while ($row = $donations->fetch_assoc()):
                    if ($row['status'] === 'completed') $total += $row['amount'];
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['donor_name']) ?></td>
                    <td><?= htmlspecialchars($row['beneficiary_name'] ?? '—') ?></td>
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
                    <td>
                        <?php if ($row['status'] !== 'completed'): ?>
                            <a href="?id=<?= $row['id'] ?>&status=completed" class="btn btn-success btn-sm">✓</a>
                        <?php endif; ?>
                        <?php if ($row['status'] !== 'cancelled'): ?>
                            <a href="?id=<?= $row['id'] ?>&status=cancelled" class="btn btn-danger btn-sm confirm-delete">✕</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr style="background:#eaf2fb;font-weight:700;">
                    <td colspan="4" style="padding:10px 14px;">Total Completed</td>
                    <td colspan="6" style="padding:10px 14px;"><?= formatCurrency($total) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
