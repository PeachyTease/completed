<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hope Harbor</title>
  <!-- Paste your CSS/JS links from React build here -->
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <!-- Your original React header HTML goes here -->
  <header>
    <nav>
      <ul>
        <li><a href="/index.php">Home</a></li>
        <li><a href="/programs.php">Programs</a></li>
        <li><a href="/stories.php">Stories</a></li>
        <li><a href="/donate.php">Donate</a></li>
        <li><a href="/contact.php">Contact</a></li>
        <?php if(isset($_SESSION['admin'])): ?>
          <li><a href="/admin/dashboard.php">Dashboard</a></li>
          <li><a href="/admin/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="/admin/login.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>
