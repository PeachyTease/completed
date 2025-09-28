<?php
// Create a PayPal order and redirect user to approval URL
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Basic input sanitization
$amount = number_format(floatval($_POST['amount'] ?? 0), 2, '.', '');
$program = substr(trim($_POST['program'] ?? ''), 0, 255);
$name = substr(trim($_POST['name'] ?? ''), 0, 255);
$email = substr(trim($_POST['email'] ?? ''), 0, 255);

// Obtain PayPal access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json","Accept-Language: en_US"]);
$response = curl_exec($ch);
if ($response === false) {
    die("PayPal auth error: " . curl_error($ch));
}
$auth = json_decode($response, true);
$access_token = $auth['access_token'] ?? null;
curl_close($ch);

if (!$access_token) {
    die("Failed to obtain PayPal access token.");
}

// Create order
$createOrder = [
    "intent" => "CAPTURE",
    "purchase_units" => [
        [
            "amount" => [
                "currency_code" => "PHP",
                "value" => $amount
            ],
            "description" => "Donation for $program"
        ]
    ],
    "application_context" => [
        "return_url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/api/capture_order.php",
        "cancel_url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}/donate_cancel.php"
    ]
];

$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($createOrder));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);
$orderResp = curl_exec($ch);
if ($orderResp === false) { die("PayPal create order error: ".curl_error($ch)); }
$orderObj = json_decode($orderResp, true);
curl_close($ch);

if (isset($orderObj['id'])) {
    // Save a pending donation record in DB (optional)
    try {
        $db = get_db();
        $stmt = $db->prepare('INSERT INTO donations (order_id, amount, program, name, email, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$orderObj['id'], $amount, $program, $name, $email, 'PENDING']);
    } catch (Exception $e) {
        // Log error - continue
    }
    // Redirect to approval URL
    $approveLink = null;
    if (isset($orderObj['links']) && is_array($orderObj['links'])) {
        foreach ($orderObj['links'] as $l) {
            if ($l['rel'] === 'approve') { $approveLink = $l['href']; break; }
        }
    }
    if ($approveLink) {
        header('Location: ' . $approveLink);
        exit;
    } else {
        echo "Order created but approval link missing.";
        var_export($orderObj);
        exit;
    }
} else {
    echo "Failed to create PayPal order.";
    var_export($orderObj);
    exit;
}
?>