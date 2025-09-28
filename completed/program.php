<?php
include 'inc/db.php';
include 'header.php';
$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM programs WHERE id=$id");
if(!$res || $res->num_rows==0){ echo "<p>Program not found.</p>"; include 'footer.php'; exit(); }
$program = $res->fetch_assoc();
?>
<h1><?php echo htmlspecialchars($program['title']); ?></h1>
<img src="<?php echo htmlspecialchars($program['image_url']); ?>" style="max-width:400px;"><br>
<p><?php echo nl2br(htmlspecialchars($program['description'])); ?></p>
<?php include 'footer.php'; ?>
