<?php
include 'inc/db.php';
include 'header.php';

$stories = $conn->query("SELECT * FROM stories ORDER BY created_at DESC");
?>
<h1>Stories</h1>
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:20px;">
  <?php while($row = $stories->fetch_assoc()): ?>
    <div style="border:1px solid #ddd; padding:15px; border-radius:8px;">
      <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="" style="width:100%; height:180px; object-fit:cover; border-radius:6px;">
      <h3><?php echo htmlspecialchars($row['title']); ?></h3>
      <p><?php echo substr(strip_tags($row['content']),0,100); ?>...</p>
      <a href="story.php?id=<?php echo $row['id']; ?>">Read More</a>
    </div>
  <?php endwhile; ?>
</div>
<?php include 'footer.php'; ?>
