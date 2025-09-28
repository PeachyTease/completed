<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include '../inc/db.php';
include '../header.php';

// Handle CSV export
if(isset($_GET['export']) && $_GET['export'] === 'csv') {
    $filename = "donations_export_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['ID','Donor Name','Email','Amount','Method','Date']);
    $res = $conn->query("SELECT id, donor_name, donor_email, amount, method, created_at FROM donations ORDER BY created_at DESC");
    while($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Filtering
$method_filter = $_GET['method'] ?? '';
$sql = "SELECT * FROM donations";
if($method_filter) {
    $stmt = $conn->prepare($sql . " WHERE method=? ORDER BY created_at DESC");
    $stmt->bind_param("s", $method_filter);
    $stmt->execute();
    $donations = $stmt->get_result();
} else {
    $donations = $conn->query($sql . " ORDER BY created_at DESC");
}
?>
<h1>All Donations</h1>
<form method="get">
  <label for="method">Filter by Method:</label>
  <select name="method" id="method">
    <option value="">All</option>
    <option value="paypal" <?php if($method_filter==='paypal') echo 'selected'; ?>>PayPal</option>
    <option value="stripe" <?php if($method_filter==='stripe') echo 'selected'; ?>>Stripe</option>
    <option value="gcash" <?php if($method_filter==='gcash') echo 'selected'; ?>>GCash</option>
  </select>
  <button type="submit">Filter</button>
</form>
<a href="donations.php?export=csv">Export as CSV</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Donor</th><th>Email</th><th>Amount</th><th>Method</th><th>Date</th></tr>
<?php while($row = $donations->fetch_assoc()): ?>
<tr>
  <td><?php echo $row['id']; ?></td>
  <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
  <td><?php echo htmlspecialchars($row['donor_email']); ?></td>
  <td>$<?php echo number_format($row['amount'],2); ?></td>
  <td><?php echo ucfirst($row['method']); ?></td>
  <td><?php echo $row['created_at']; ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php include '../footer.php'; ?>
