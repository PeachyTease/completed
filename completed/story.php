<?php
include 'inc/db.php';
include 'header.php';

$id = intval($_GET['id']);
$story = $conn->query("SELECT * FROM stories WHERE id=$id")->fetch_assoc();
if(!$story) { echo "<p>Story not found.</p>"; include 'footer.php'; exit(); }
?>
<article>
  <h1><?php echo htmlspecialchars($story['title']); ?></h1>
  <img src="<?php echo htmlspecialchars($story['image']); ?>" alt="" style="width:100%; max-height:400px; object-fit:cover; margin:20px 0;">
  <div><?php echo nl2br($story['content']); ?></div>
</article>
<?php include 'footer.php'; ?>
