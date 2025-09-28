<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include '../inc/db.php';
include '../header.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $image = $_POST['image'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO stories (title, image, content) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $image, $content);
    $stmt->execute();
    header("Location: stories.php");
    exit();
}
?>
<h1>Add Story</h1>
<form method="post">
  <p>Title: <input type="text" name="title" required></p>
  <p>Image URL: <input type="text" name="image" required></p>
  <p>Content:<br><textarea name="content" rows="8" cols="50" required></textarea></p>
  <button type="submit">Save</button>
</form>
<?php include '../footer.php'; ?>
