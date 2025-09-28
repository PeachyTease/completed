<?php
include 'inc/db.php';
include 'header.php';

$success = false;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();

    // Optional: send email (comment out if GoDaddy blocks mail())
    // mail("admin@yourdomain.com", "New Contact Message from $name", $message, "From: $email");

    $success = true;
}
?>

<h1>Contact Us</h1>

<?php if($success): ?>
  <p style="color:green;">Thank you! Your message has been sent.</p>
<?php endif; ?>

<form method="post">
  <p>Name: <input type="text" name="name" required></p>
  <p>Email: <input type="email" name="email" required></p>
  <p>Message:<br>
     <textarea name="message" rows="6" cols="50" required></textarea>
  </p>
  <button type="submit">Send</button>
</form>

<?php include 'footer.php'; ?>
