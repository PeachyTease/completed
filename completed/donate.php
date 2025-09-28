<?php
include 'inc/header.php';
?>
<main>
  <h2>Donate</h2>
  <form id="donationForm" action="/api/create_order.php" method="post">
    <label>Amount (PHP): <input type="number" name="amount" value="100" required></label><br>
    <label>Program: <input type="text" name="program" value="General" required></label><br>
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Donate (Sandbox)</button>
  </form>
</main>
<?php include 'inc/footer.php'; ?>

<hr>
<h3>Or pay with Stripe (card)</h3>
<form action="/api/stripe_create.php" method="post">
  <input type="hidden" name="amount" value="100">
  <input type="hidden" name="program" value="General">
  <input type="hidden" name="name" value="">
  <input type="hidden" name="email" value="">
  <button type="submit">Pay with Card (Stripe)</button>
</form>

<hr>
<h3>Or pay with GCash</h3>
<form action="/api/gcash_donate.php" method="post">
  <label>Amount: <input name="amount" value="100" required></label><br>
  <label>GCash Number: <input name="gcash_number" required></label><br>
  <label>Transaction No (if available): <input name="transaction_no"></label><br>
  <label>Name: <input name="name"></label><br>
  <label>Email: <input name="email"></label><br>
  <button type="submit">Donate via GCash (auto-accept)</button>
</form>
