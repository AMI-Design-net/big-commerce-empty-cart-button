<?php
// empty-cart.php
// === Allow requests from your store domain ===
header("Access-Control-Allow-Origin: https://rockofftrade.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");

// === CONFIG ===
$token = "XXXXX--XXXXX";
$store_hash = "XXXX--XXXX";

// === Input validation ===
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(["success" => false, "error" => "Missing cart ID"]);
    exit;
}

$cartID = $_POST['id']; //preg_replace('/[^a-zA-Z0-9\-]/', '', $_POST['id']); // sanitize

// === cURL request to BigCommerce API ===
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.bigcommerce.com/stores/$store_hash/v3/carts/$cartID",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "DELETE",
  CURLOPT_HTTPHEADER => [
    "Accept: application/json",
    "Content-Type: application/json",
    "X-Auth-Token: $token"
  ],
]);

$response = curl_exec($curl);
$err      = curl_error($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

// === Error handling ===
if ($err) {
    echo json_encode(["success" => false, "error" => "cURL Error: $err"]);
    exit;
}

if ($httpcode == 204) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "status"  => $httpcode,
        "response"=> $response
    ]);
}