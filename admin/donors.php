<?php
session_start();
require_once '../includes/config.php';
requireAdmin();

$pageTitle = "Donors – Admin";
$role = 'admin';
$msg = '';
$error = '';

// Delete donor
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM donors WHERE id = $id");
    $msg = "Donor deleted successfully.";
}

// Add donor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donor'])) {
    $name    = sanitize($conn, $_POST['name']);
    $email   = sanitize($conn, $_POST['email']);
    $phone   = sanitize($conn, $_POST['phone']);
    $address = sanitize($conn, $_POST['address']);
    $pass    = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($name && $email && $_POST['password']) {
        $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, address) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $email, $pass, $phone, $address);
        if ($stmt->execute()) {
            $msg = "Donor added successfully.";
        } else {
            $error = "Email already exists or database error.";
        }
        $stmt->close();
    } else {
        $error = "Name, email and password are required.";
    }
}

$donors = $conn->query("
    SELECT d.*, COALESCE(SUM(dn.amount),0) as total_donated, COUNT(dn.id) as donation_count
    FROM donors d
    LEFT JOIN donations dn ON dn.donor_id = d.id AND dn.status = 'completed'
    GROUP BY d.id
    ORDER BY d.created_at DESC
");

include '../includes/header.php';
?>

<div class="flex-between page-title">
    <span>👥 Donors</span>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='flex'">+ Add Donor</button>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Donations</th>
                    <th>Total Donated</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $donors->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                    <td><?= $row['donation_count'] ?></td>
                    <td><?= formatCurrency($row['total_donated']) ?></td>
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm confirm-delete">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Donor Modal -->
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;">
    <div class="card" style="width:100%;max-width:500px;margin:0 20px;">
        <div class="flex-between" style="margin-bottom:16px;">
            <div class="card-title" style="margin:0;border:none;">Add New Donor</div>
            <button onclick="document.getElementById('addModal').style.display='none'" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">✕</button>
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone">
                </div>
                <div class="form-group full">
                    <label>Address</label>
                    <textarea name="address"></textarea>
                </div>
            </div>
            <div style="margin-top:16px;">
                <button type="submit" name="add_donor" class="btn btn-success">Add Donor</button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
