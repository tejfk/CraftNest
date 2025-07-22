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

$product_name = trim($_POST['product_name'] ?? '');
$product_description = trim($_POST['product_description'] ?? '');
$product_price = trim($_POST['product_price'] ?? '');
$product_stock = trim($_POST['product_stock'] ?? '');
$image_url = '';

// ... (Validation remains the same) ...
if (empty($product_name)) { $response['errors'][] = "Product name is required."; }
if (empty($product_price) || !is_numeric($product_price) || $product_price < 0) { $response['errors'][] = "A valid, positive price is required."; }
if (empty($product_stock) || !is_numeric($product_stock) || $product_stock < 0) { $response['errors'][] = "A valid stock quantity is required."; }

if (isset($_FILES["product_image"]) && $_FILES["product_image"]["error"] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
    $file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid('prod_') . "." . $file_type;
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        $image_url = 'uploads/' . basename($target_file);
    } else {
        $response['errors'][] = "Server error: Could not move uploaded file.";
    }
} else {
    $response['errors'][] = "Product image is required.";
}

if (!empty($response['errors'])) {
    echo json_encode($response);
    exit();
}

// FIXED: Added 'status' to the query and set it to 'active' by default
$query = "INSERT INTO products (user_id, name, description, price, stock, image_url, is_for_auction, status) VALUES (?, ?, ?, ?, ?, ?, 0, 'active')";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("issdis", $user_id, $product_name, $product_description, $product_price, $product_stock, $image_url);
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Product added successfully!';
    } else {
        $response['errors'][] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['errors'][] = "Error preparing database statement.";
}

$conn->close();
echo json_encode($response);
?>