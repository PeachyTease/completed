<?php
session_start();
require_once '../inc/db.php'; // database connection

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect if not owner
if ($_SESSION['role'] !== 'owner') {
    header("Location: dashboard.php");
    exit;
}

// Handle add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'admin';

    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role]);
}

// Handle delete admin
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // prevent deleting owner
    $stmt = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $role = $stmt->fetchColumn();

    if ($role !== 'owner') {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: manage_admins.php");
    exit;
}

// Handle reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $id = (int) $_POST['id'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // prevent resetting owner’s password here (for safety, should be manual in DB)
    $stmt = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $role = $stmt->fetchColumn();

    if ($role !== 'owner') {
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$newPassword, $id]);
    }
    header("Location: manage_admins.php");
    exit;
}

// Fetch all admins
$stmt = $pdo->query("SELECT id, name, email, role, created_at FROM admins ORDER BY created_at DESC");
$admins = $stmt->fetchAll();
?>

<?php include '../inc/header.php'; ?>
<main>
  <h1>Manage Admin Accounts</h1>

  <h2>Add New Admin</h2>
  <form method="POST">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <div class="form-actions">
      <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
    </div>
  </form>

  <h2>Current Admins</h2>
  <table border="1" cellpadding="10" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($admins as $admin): ?>
      <tr>
        <td><?= htmlspecialchars($admin['id']) ?></td>
        <td><?= htmlspecialchars($admin['name']) ?></td>
        <td><?= htmlspecialchars($admin['email']) ?></td>
        <td><?= htmlspecialchars($admin['role']) ?></td>
        <td><?= htmlspecialchars($admin['created_at']) ?></td>
        <td>
          <?php if ($admin['role'] !== 'owner'): ?>
            <!-- Delete link -->
            <a href="manage_admins.php?delete=<?= $admin['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>

            <!-- Reset password form -->
            <form method="POST" style="display:inline-block; margin-left:8px;">
              <input type="hidden" name="id" value="<?= $admin['id'] ?>">
              <input type="password" name="new_password" placeholder="New Password" required>
              <button type="submit" name="reset_password" class="btn btn-secondary">Reset</button>
            </form>
          <?php else: ?>
            <em>Owner</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</main>
<?php include '../inc/footer.php'; ?>
