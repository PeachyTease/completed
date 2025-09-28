<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../inc/db.php';
include '../header.php';

$stories = $conn->query("SELECT * FROM stories ORDER BY created_at DESC");
?>
<h1>Manage Stories</h1>
<a href="story_add.php">+ Add Story</a>
<table border="1" cellpadding="10" cellspacing="0">
  <tr>
    <th>ID</th><th>Title</th><th>Created</th><th>Actions</th>
  </tr>
  <?php while($row = $stories->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo $row['created_at']; ?></td>
      <td>
        <a href="story_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
        <a href="story_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this story?')">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>
<?php include '../footer.php'; ?>
