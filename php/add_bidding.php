<?php
require_once 'db_connect.php';
session_start();
header('Content-Type: application/json');

$response = ['status' => 'error', 'errors' => []];

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    $response['errors'][] = 'Authentication failed.';
    echo json_encode($response);
    exit();
}
$user_id = $_SESSION['user_id'];

// ... (Validation remains the same) ...
$name = trim($_POST['bidding_name'] ?? '');
$description = trim($_POST['bidding_description'] ?? '');
$start_bid = trim($_POST['bidding_start_bid'] ?? '');
$buy_now_price = trim($_POST['bidding_buy_now'] ?? null);
$end_time = $_POST['bidding_end_time'] ?? '';
$image_url = '';

if ($buy_now_price === '') { $buy_now_price = null; }

if (isset($_FILES["bidding_image"]) && $_FILES["bidding_image"]["error"] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
    $file_type = strtolower(pathinfo($_FILES["bidding_image"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid('bid_') . "." . $file_type;
    if (move_uploaded_file($_FILES["bidding_image"]["tmp_name"], $target_file)) {
        $image_url = 'uploads/' . basename($target_file);
    } else {
        $response['errors'][] = "Server error: Could not move uploaded file.";
    }
}

if (!empty($response['errors'])) {
    echo json_encode($response);
    exit();
}

// FIXED: Added 'status' to the query and set it to 'active' by default
$query = "INSERT INTO products (user_id, name, description, start_bid, buy_now_price, image_url, auction_end_time, is_for_auction, stock, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1, 0, 'active')";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("issdsss", $user_id, $name, $description, $start_bid, $buy_now_price, $image_url, $end_time);
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Bidding product added successfully!';
    } else {
        $response['errors'][] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['errors'][] = "Database preparation error.";
}

$conn->close();
echo json_encode($response);
?>