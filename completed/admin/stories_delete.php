<?php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';
if (!isset($_SESSION['admin_id'])) { header('Location: /admin/login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
if ($id) {
    try {
        $db = get_db();
        $stmt = $db->prepare('SELECT image FROM stories WHERE id=? LIMIT 1'); $stmt->execute([$id]); $row = $stmt->fetch();
        if ($row && !empty($row['image'])) { @unlink(__DIR__ . '/../assets/images/' . $row['image']); }
        $stmt = $db->prepare('DELETE FROM stories WHERE id=?'); $stmt->execute([$id]);
    } catch (Exception $e) {}
}
header('Location: /admin/stories.php'); exit;
?>