<?php
session_start();
require_once '../inc/db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Extract session values
$adminId = $_SESSION['admin']['id'];
$role    = $_SESSION['admin']['role'] ?? 'admin';

// Donations
$result = $conn->query("SELECT SUM(amount) as total, COUNT(*) as cnt FROM donations");
$row = $result->fetch_assoc();
$totalDonations = $row['total'] ?? 0;
$donationCount  = $row['cnt'] ?? 0;

// Programs
$programCount = $conn->query("SELECT COUNT(*) as cnt FROM programs")->fetch_assoc()['cnt'] ?? 0;

// Stories
$storyCount = $conn->query("SELECT COUNT(*) as cnt FROM stories")->fetch_assoc()['cnt'] ?? 0;

include '../inc/header.php';
?>
<main>
  <h1>Admin Dashboard</h1>
  <div style="display:flex; gap:20px; flex-wrap:wrap;">

    <div style="border:1px solid #ccc; padding:15px; width:220px; border-radius:8px; background:#fff;">
      <h3>Donations</h3>
      <p>Total: ₱<?= number_format($totalDonations, 2) ?></p>
      <p>Transactions: <?= $donationCount ?></p>
      <a href="donations.php" class="btn btn-primary">View Donations</a>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:220px; border-radius:8px; background:#fff;">
      <h3>Programs</h3>
      <p>Total: <?= $programCount ?></p>
      <a href="programs.php" class="btn btn-primary">Manage Programs</a>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:220px; border-radius:8px; background:#fff;">
      <h3>Stories</h3>
      <p>Total: <?= $storyCount ?></p>
      <a href="stories.php" class="btn btn-primary">Manage Stories</a>
    </div>

    <div style="border:1px solid #ccc; padding:15px; width:220px; border-radius:8px; background:#fff;">
      <h3>Account</h3>
      <a href="change_password.php" class="btn btn-secondary">Change My Password</a><br><br>
      <?php if ($role === 'owner'): ?>
        <a href="manage_admins.php" class="btn btn-danger">Manage Admins</a>
      <?php endif; ?>
      <br><br>
      <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>

  </div>
</main>
<?php include '../inc/footer.php'; ?>
