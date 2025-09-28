<?php
if (!isset($_SESSION)) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel – Hands With Care</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="icon" type="image/png" href="/inc/favicon.png">
  <link rel="stylesheet" href="../css/admin.css">
  <!-- Google Fonts (same as public for consistency) -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Roboto', sans-serif; margin:0; background:#f9f9f9; }
    header { background:#333; color:#fff; padding:10px 20px; }
    .admin-nav { display:flex; gap:15px; }
    .admin-nav a { color:#fff; text-decoration:none; font-weight:500; }
    .admin-nav a:hover { text-decoration:underline; }
  </style>
</head>
<body>
<header>
  <div class="header-container">
    <h1 style="margin:0; font-size:20px;">Admin Panel – Hands With Care</h1>
    <nav class="admin-nav">
      <a href="/admin/dashboard.php">Dashboard</a>
      <a href="/admin/donations.php">Donations</a>
      <a href="/admin/programs.php">Programs</a>
      <a href="/admin/stories.php">Stories</a>
      <a href="/admin/change_password.php">Change Password</a>
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin']['role'] === 'owner'): ?>
        <a href="/admin/manage_admins.php" style="color:#ff6666;">Manage Admins</a>
      <?php endif; ?>
      <a href="/admin/logout.php" style="color:#ffcc00;">Logout</a>
    </nav>
  </div>
</header>
<main style="padding:20px;">
