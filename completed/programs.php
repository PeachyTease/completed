<?php
include 'inc/db.php';
include 'header.php';
$res = $conn->query("SELECT * FROM programs ORDER BY created_at DESC");
?>
<h1>Our Programs</h1>
<div style="display:flex; flex-wrap:wrap; gap:20px;">
<?php while($row=$res->fetch_assoc()): ?>
  <div style="border:1px solid #ccc; padding:10px; width:250px;">
    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" style="max-width:100%;">
    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
    <p><?php echo htmlspecialchars(substr($row['description'],0,100)); ?>...</p>
    <a href="program.php?id=<?php echo $row['id']; ?>">Read More</a>
  </div>
<?php endwhile; ?>
</div>
<?php include 'footer.php'; ?>
