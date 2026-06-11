<?php
session_start();
require_once '../includes/config.php';
requireDonor();

$pageTitle = "Make a Donation";
$role = 'donor';
$donorId = $_SESSION['donor_id'];
$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount     = (float)$_POST['amount'];
    $catId      = (int)$_POST['category_id'];
    $benId      = !empty($_POST['beneficiary_id']) ? (int)$_POST['beneficiary_id'] : null;
    $method     = sanitize($conn, $_POST['payment_method']);
    $note       = sanitize($conn, $_POST['note']);
    $benIdParam = $benId ?: null;

    if ($amount <= 0) {
        $error = "Please enter a valid amount.";
    } elseif (!in_array($method, ['cash','bank_transfer','upi','cheque'])) {
        $error = "Invalid payment method.";
    } else {
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, category_id, beneficiary_id, amount, payment_method, note) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iiidss", $donorId, $catId, $benIdParam, $amount, $method, $note);
        if ($stmt->execute()) {
            $msg = "Thank you! Your donation of " . formatCurrency($amount) . " has been recorded.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

$categories    = $conn->query("SELECT * FROM categories ORDER BY name");
$beneficiaries = $conn->query("SELECT * FROM beneficiaries WHERE status='active' ORDER BY name");

include '../includes/header.php';
?>

<div class="page-title">💚 Make a Donation</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<div class="card" style="max-width:620px;">
    <div class="card-title">Donation Details</div>
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Amount (₹) *</label>
                <input type="number" name="amount" min="1" step="0.01" placeholder="e.g. 500" required
                       value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="">Select method</option>
                    <option value="upi"           <?= ($_POST['payment_method'] ?? '') === 'upi'           ? 'selected':'' ?>>UPI</option>
                    <option value="bank_transfer"  <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected':'' ?>>Bank Transfer</option>
                    <option value="cash"           <?= ($_POST['payment_method'] ?? '') === 'cash'          ? 'selected':'' ?>>Cash</option>
                    <option value="cheque"         <?= ($_POST['payment_method'] ?? '') === 'cheque'        ? 'selected':'' ?>>Cheque</option>
                </select>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">Select category</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($_POST['category_id'] ?? '') == $c['id'] ? 'selected':'' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Beneficiary (Optional)</label>
                <select name="beneficiary_id">
                    <option value="">General fund</option>
                    <?php while ($b = $beneficiaries->fetch_assoc()): ?>
                        <option value="<?= $b['id'] ?>" <?= ($_POST['beneficiary_id'] ?? '') == $b['id'] ? 'selected':'' ?>>
                            <?= htmlspecialchars($b['name']) ?> (<?= ucfirst($b['category']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group full">
                <label>Note / Message</label>
                <textarea name="note" placeholder="Add a personal message (optional)"><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
            </div>
        </div>
        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-success">💚 Confirm Donation</button>
            <a href="/charity-management-system/donor/dashboard.php" class="btn" style="background:#eee;color:#333;margin-left:10px;">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
