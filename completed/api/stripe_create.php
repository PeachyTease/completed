<?php
// Create Stripe Checkout Session (server-side) using Stripe Secret Key
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo 'Method not allowed'; exit;
}

$amount = number_format(floatval($_POST['amount'] ?? 0), 2, '.', '');
$program = substr(trim($_POST['program'] ?? ''), 0, 255);
$name = substr(trim($_POST['name'] ?? ''), 0, 255);
$email = substr(trim($_POST['email'] ?? ''), 0, 255);

if (empty(STRIPE_SECRET_KEY) || STRIPE_SECRET_KEY === 'REPLACE_WITH_STRIPE_TEST_KEY') {
    die('Stripe not configured.');
}

// Stripe expects amount in smallest currency unit (PHP has 2 decimals, so multiply by 100)
$amount_cents = intval(round(floatval($amount) * 100));

// Create Checkout Session via Stripe API
$data = http_build_query([
    'payment_method_types[]' => 'card',
    'mode' => 'payment',
    'line_items[0][price_data][currency]' => 'php',
    'line_items[0][price_data][product_data][name]' => 'Donation for ' . $program,
    'line_items[0][price_data][unit_amount]' => $amount_cents,
    'line_items[0][quantity]' => 1,
    'success_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/stripe_success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/donate_cancel.php'
]);

$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
if ($response === false) { die('Stripe API error: ' . curl_error($ch)); }
$respObj = json_decode($response, true);
curl_close($ch);

if (isset($respObj['url'])) {
    // Save pending donation
    try {
        $db = get_db();
        $stmt = $db->prepare('INSERT INTO donations (order_id, amount, program, name, email, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$respObj['id'] ?? null, $amount, $program, $name, $email, 'PENDING']);
    } catch (Exception $e) {
        // ignore
    }
    header('Location: ' . $respObj['url']);
    exit;
} else {
    echo 'Failed to create Stripe checkout session.';
    var_export($respObj);
    exit;
}
?>