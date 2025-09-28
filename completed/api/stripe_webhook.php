<?php
// Stripe webhook endpoint to listen for payment_intent.succeeded or checkout.session.completed
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

// Read raw payload
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
// Optionally verify signature with STRIPE_WEBHOOK_SECRET if provided
// For now, parse event without verification (configure verification in production)

$event = json_decode($payload, true);
if (!$event) {
    http_response_code(400);
    echo 'Invalid payload';
    exit;
}

$type = $event['type'] ?? '';

if ($type === 'checkout.session.completed' || $type === 'payment_intent.succeeded') {
    // Extract session id or payment intent id
    $session_id = $event['data']['object']['id'] ?? null;
    $amount_total = $event['data']['object']['amount_total'] ?? ($event['data']['object']['amount'] ?? null);
    $currency = $event['data']['object']['currency'] ?? null;
    // Update DB: find pending donation with order_id == session_id
    try {
        $db = get_db();
        $stmt = $db->prepare('UPDATE donations SET status = ?, captured_response = ? WHERE order_id = ?');
        $stmt->execute(['COMPLETED', $payload, $session_id]);
    } catch (Exception $e) {
        // ignore
    }
}

http_response_code(200);
echo 'ok';
?>