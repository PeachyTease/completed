<?php
// Accept contact form submissions and store in DB
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$name = substr(trim($_POST['name'] ?? ''), 0, 255);
$email = substr(trim($_POST['email'] ?? ''), 0, 255);
$message = substr(trim($_POST['message'] ?? ''), 0, 2000);

try {
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $message]);
    header('Location: /contact_thanks.php');
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to save message.";
    exit;
}
?>