<?php
// After successful checkout, Stripe redirects to success URL with session_id
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    die('Missing session_id');
}

// Retrieve session to verify (optional) using Stripe API
if (empty(STRIPE_SECRET_KEY) || STRIPE_SECRET_KEY === 'REPLACE_WITH_STRIPE_TEST_KEY') {
    die('Stripe not configured.');
}

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . urlencode($session_id));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
$response = curl_exec($ch);
if ($response === false) { die('Stripe API error: ' . curl_error($ch)); }
$session = json_decode($response, true);
curl_close($ch);

// Update donation record if exists
try {
    $db = get_db();
    $stmt = $db->prepare('UPDATE donations SET status = ?, captured_response = ?, updated_at = NOW() WHERE order_id = ?');
    $stmt->execute(['COMPLETED', json_encode($session), $session_id]);
} catch (Exception $e) {
    // ignore
}

header('Location: /donate_success.php');
exit;
?>