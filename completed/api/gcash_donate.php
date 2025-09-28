<?php
// Simple GCash donation handler that records donation immediately without requiring admin confirmation.
// NOTE: Auto-accepting GCash transaction numbers is insecure; implement proper verification with GCash in production.
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo 'Method not allowed'; exit;
}

$amount = number_format(floatval($_POST['amount'] ?? 0), 2, '.', '');
$program = substr(trim($_POST['program'] ?? ''), 0, 255);
$name = substr(trim($_POST['name'] ?? ''), 0, 255);
$email = substr(trim($_POST['email'] ?? ''), 0, 255);
$gcash_number = substr(trim($_POST['gcash_number'] ?? ''), 0, 50);
$transaction_no = substr(trim($_POST['transaction_no'] ?? ''), 0, 200);

// Prepare captured_response
$captured = [
    'provider' => 'GCash',
    'gcash_number' => $gcash_number,
    'transaction_no' => $transaction_no,
    'note' => 'Auto-accepted by system as requested'
];

try {
    $db = get_db();
    // Use generated unique id as order_id (prefix GCS- + timestamp)
    $order_id = 'GCS-' . time() . '-' . substr(md5(uniqid('', true)),0,6);
    $stmt = $db->prepare('INSERT INTO donations (order_id, amount, program, name, email, status, captured_response, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$order_id, $amount, $program, $name, $email, 'COMPLETED', json_encode($captured)]);
} catch (Exception $e) {
    http_response_code(500); echo 'DB error'; exit;
}

// Show success page
header('Location: /donate_success.php');
exit;
?>