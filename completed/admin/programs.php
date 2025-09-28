<?php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: /admin/login.php'); exit; }
$db = get_db();
$items = [];
try { $stmt = $db->query('SELECT id, title, created_at FROM programs ORDER BY created_at DESC'); $items = $stmt->fetchAll(); } catch (Exception $e) { $items = []; }
include __DIR__ . '/../inc/header.php';
?>
<main>
  <h2>Manage Programs</h2>
  <a href="/admin/programs_add.php">Add New Program</a>
  <table border="1" cellpadding="6">
    <tr><th>ID</th><th>Title</th><th>Created</th><th>Actions</th></tr>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?php echo htmlspecialchars($it['id']); ?></td>
        <td><?php echo htmlspecialchars($it['title']); ?></td>
        <td><?php echo htmlspecialchars($it['created_at']); ?></td>
        <td>
          <a href="/admin/programs_edit.php?id=<?php echo $it['id']; ?>">Edit</a> |
          <a href="/admin/programs_delete.php?id=<?php echo $it['id']; ?>" onclick="return confirm('Delete this program?');">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>