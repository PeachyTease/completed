<?php
// Capture PayPal order after user approval. PayPal redirects user here with token parameter (order ID)
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

$token = $_GET['token'] ?? $_POST['token'] ?? null;
if (!$token) {
    die('Missing order token.');
}

// Obtain access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json","Accept-Language: en_US"]);
$response = curl_exec($ch);
if ($response === false) { die("PayPal auth error: " . curl_error($ch)); }
$auth = json_decode($response, true);
$access_token = $auth['access_token'] ?? null;
curl_close($ch);
if (!$access_token) { die("Failed to obtain PayPal access token."); }

// Capture order
$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$token}/capture");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);
$captureResp = curl_exec($ch);
if ($captureResp === false) { die("PayPal capture error: ".curl_error($ch)); }
$captureObj = json_decode($captureResp, true);
curl_close($ch);

// Update DB record
try {
    $db = get_db();
    $status = $captureObj['status'] ?? 'COMPLETED';
    $stmt = $db->prepare('UPDATE donations SET status = ?, captured_response = ?, updated_at = NOW() WHERE order_id = ?');
    $stmt->execute([$status, json_encode($captureObj), $token]);
} catch (Exception $e) {
    // log error
}

// Show success page
header('Location: /donate_success.php');
exit;
?>