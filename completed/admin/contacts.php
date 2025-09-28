<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../inc/db.php';
include '../header.php';

$contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
?>
<h1>Contact Messages</h1>
<table border="1" cellpadding="10" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Date</th>
  </tr>
  <?php while($row = $contacts->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo htmlspecialchars($row['name']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
      <td><?php echo $row['created_at']; ?></td>
    </tr>
  <?php endwhile; ?>
</table>
<?php include '../footer.php'; ?>
