<?php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: /admin/login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$db = get_db();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = substr(trim($_POST['title'] ?? ''),0,255);
    $desc = trim($_POST['description'] ?? '');
    try {
        if (!empty($_FILES['image']['name'])) {
            $tmp = $_FILES['image']['tmp_name'];
            $image_name = time().'_'.basename($_FILES['image']['name']);
            move_uploaded_file($tmp, __DIR__ . '/../assets/images/' . $image_name);
            $stmt = $db->prepare('UPDATE programs SET title=?, description=?, image=? WHERE id=?');
            $stmt->execute([$title, $desc, $image_name, $id]);
        } else {
            $stmt = $db->prepare('UPDATE programs SET title=?, description=? WHERE id=?');
            $stmt->execute([$title, $desc, $id]);
        }
        header('Location: /admin/programs.php'); exit;
    } catch (Exception $e) { $err='DB error'; }
} else {
    $stmt = $db->prepare('SELECT id, title, description, image FROM programs WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
}
include __DIR__ . '/../inc/header.php';
?>
<main>
  <h2>Edit Program</h2>
  <?php if ($err) echo "<p style='color:red;'>$err</p>"; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Title: <input name="title" value="<?php echo htmlspecialchars($row['title'] ?? '') ?>" required></label><br>
    <label>Description:<br><textarea name="description" required><?php echo htmlspecialchars($row['description'] ?? '') ?></textarea></label><br>
    <?php if (!empty($row['image'])): ?>
      <img src="/assets/images/<?php echo htmlspecialchars($row['image']); ?>" style="max-width:150px;"><br>
    <?php endif; ?>
    <label>Replace Image: <input type="file" name="image"></label><br>
    <button type="submit">Save</button>
  </form>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>