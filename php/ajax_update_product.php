<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Security & Authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_seller']) || $_SESSION['is_seller'] != 1) {
    $response['message'] = 'Authentication failed.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];

    // Fetch original product data for validation and image path
    $stmt = $conn->prepare("SELECT user_id, image_url, is_for_auction FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $original_product = $result->fetch_assoc();
    $stmt->close();

    if (!$original_product || $original_product['user_id'] != $user_id) {
        $response['message'] = 'Authorization error.';
        echo json_encode($response);
        exit();
    }

    // Common fields
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_url = $original_product['image_url']; // Default to old image

    // Handle new image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $file_type = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid('prod_') . "." . $file_type;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete the old image if it exists and is not a placeholder
            if (!empty($image_url) && file_exists('../' . $image_url)) {
                unlink('../' . $image_url);
            }
            $image_url = 'uploads/' . basename($target_file);
        }
    }

    // Update based on product type
    if (!$original_product['is_for_auction']) {
        // Market Product
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $update_stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE product_id = ? AND user_id = ?");
        $update_stmt->bind_param("ssdisii", $name, $description, $price, $stock, $image_url, $product_id, $user_id);
    } else {
        // Auction Product
        $start_bid = (float)$_POST['start_bid'];
        $end_time = $_POST['auction_end_time'];
        $update_stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, start_bid = ?, auction_end_time = ?, image_url = ? WHERE product_id = ? AND user_id = ?");
        $update_stmt->bind_param("ssdssii", $name, $description, $start_bid, $end_time, $image_url, $product_id, $user_id);
    }

    if ($update_stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Product updated successfully!';
    } else {
        $response['message'] = 'Database update failed: ' . $update_stmt->error;
    }
    $update_stmt->close();

} else {
    $response['message'] = 'Invalid request method.';
}

$conn->close();
echo json_encode($response);
?>