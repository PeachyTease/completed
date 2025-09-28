<?php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: /admin/login.php'); exit; }

$err='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = substr(trim($_POST['title'] ?? ''),0,255);
    $desc = trim($_POST['description'] ?? '');
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $tmp = $_FILES['image']['tmp_name'];
        $image_name = time().'_'.basename($_FILES['image']['name']);
        move_uploaded_file($tmp, __DIR__ . '/../assets/images/' . $image_name);
    }
    try {
        $db = get_db();
        $stmt = $db->prepare('INSERT INTO programs (title, description, image, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$title, $desc, $image_name]);
        header('Location: /admin/programs.php');
        exit;
    } catch (Exception $e) { $err = 'DB error'; }
}

include __DIR__ . '/../inc/header.php';
?>
<main>
  <h2>Add Program</h2>
  <?php if ($err) echo "<p style='color:red;'>$err</p>"; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Title: <input name="title" required></label><br>
    <label>Description:<br><textarea name="description" required></textarea></label><br>
    <label>Image: <input type="file" name="image"></label><br>
    <button type="submit">Add Program</button>
  </form>
</main>
<?php include __DIR__ . '/../inc/footer.php'; ?>