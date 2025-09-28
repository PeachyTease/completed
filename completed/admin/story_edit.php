<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include '../inc/db.php';
include '../header.php';

$id = intval($_GET['id']);
$story = $conn->query("SELECT * FROM stories WHERE id=$id")->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $image = $_POST['image'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE stories SET title=?, image=?, content=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $image, $content, $id);
    $stmt->execute();
    header("Location: stories.php");
    exit();
}
?>
<h1>Edit Story</h1>
<form method="post">
  <p>Title: <input type="text" name="title" value="<?php echo htmlspecialchars($story['title']); ?>" required></p>
  <p>Image URL: <input type="text" name="image" value="<?php echo htmlspecialchars($story['image']); ?>" required></p>
  <p>Content:<br><textarea name="content" rows="8" cols="50" required><?php echo htmlspecialchars($story['content']); ?></textarea></p>
  <button type="submit">Update</button>
</form>
<?php include '../footer.php'; ?>
