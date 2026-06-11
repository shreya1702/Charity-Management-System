<?php
session_start();
require_once '../includes/config.php';
requireAdmin();

$pageTitle = "Beneficiaries – Admin";
$role = 'admin';
$msg = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM beneficiaries WHERE id = $id");
    $msg = "Beneficiary removed.";
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE beneficiaries SET status = IF(status='active','inactive','active') WHERE id = $id");
    $msg = "Status updated.";
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ben'])) {
    $name     = sanitize($conn, $_POST['name']);
    $category = sanitize($conn, $_POST['category']);
    $desc     = sanitize($conn, $_POST['description']);
    $contact  = sanitize($conn, $_POST['contact']);
    $address  = sanitize($conn, $_POST['address']);

    if ($name) {
        $stmt = $conn->prepare("INSERT INTO beneficiaries (name, category, description, contact, address) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $category, $desc, $contact, $address);
        $stmt->execute() ? $msg = "Beneficiary added." : $error = "Error adding beneficiary.";
        $stmt->close();
    } else {
        $error = "Name is required.";
    }
}

$bens = $conn->query("SELECT * FROM beneficiaries ORDER BY created_at DESC");
include '../includes/header.php';
?>

<div class="flex-between page-title">
    <span>🏠 Beneficiaries</span>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">+ Add Beneficiary</button>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Name</th><th>Category</th><th>Contact</th><th>Address</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php while ($row = $bens->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['name']) ?></strong>
                        <?php if ($row['description']): ?>
                            <br><small style="color:#888;"><?= htmlspecialchars(substr($row['description'], 0, 60)) ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><?= ucfirst($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['contact'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['address'] ?? '—') ?></td>
                    <td>
                        <span class="badge badge-<?= $row['status'] === 'active' ? 'success' : 'danger' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="?toggle=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Toggle</a>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm confirm-delete">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
    <div class="card" style="width:100%;max-width:500px;margin:0 20px;">
        <div class="flex-between" style="margin-bottom:16px;">
            <div class="card-title" style="margin:0;border:none;">Add Beneficiary</div>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">✕</button>
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="individual">Individual</option>
                        <option value="family">Family</option>
                        <option value="organization">Organization</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <input type="text" name="contact">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address">
                </div>
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description"></textarea>
                </div>
            </div>
            <div style="margin-top:16px;">
                <button type="submit" name="add_ben" class="btn btn-success">Add Beneficiary</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
