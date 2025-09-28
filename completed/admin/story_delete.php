<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include '../inc/db.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM stories WHERE id=$id");
header("Location: stories.php");
exit();
