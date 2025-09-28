<?php
session_start();
require_once '../inc/db.php'; // database connection

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $message = "❌ New passwords do not match.";
    } else {
        // Fetch current password hash
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $row = $stmt->fetch();

        if ($row && password_verify($currentPassword, $row['password'])) {
            // Update with new password
            $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $_SESSION['admin_id']]);

            $message = "✅ Password updated successfully.";
        } else {
            $message = "❌ Current password is incorrect.";
        }
    }
}
?>

<?php include '../inc/header.php'; ?>
<main>
  <h1>Change Password</h1>

  <?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="current_password">Current Password</label>
    <input type="password" name="current_password" id="current_password" required>

    <label for="new_password">New Password</label>
    <input type="password" name="new_password" id="new_password" required>

    <label for="confirm_password">Confirm New Password</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Update Password</button>
    </div>
  </form>
</main>
<?php include '../inc/footer.php'; ?>
